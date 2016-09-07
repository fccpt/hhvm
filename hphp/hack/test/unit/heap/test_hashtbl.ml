(**
 * Copyright (c) 2015, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the BSD-style license found in the
 * LICENSE file in the "hack" directory of this source tree. An additional grant
 * of patent rights can be found in the PATENTS file in the same directory.
 *
*)

type key = Digest.t

external hh_add    : key -> string -> unit = "hh_add"
external hh_mem    : key -> bool           = "hh_mem"
external hh_remove : key-> unit            = "hh_remove"
external hh_move   : key -> key -> unit    = "hh_move"
external hh_get    : key -> string         = "hh_get_and_deserialize"

let expect ~msg bool =
  if bool then () else begin
    print_endline msg;
    exit 1
  end

let to_key = Digest.string

let add key value = hh_add (to_key key) (Marshal.to_string value [])
let mem key = hh_mem (to_key key)
let remove key = hh_remove (to_key key)
let move k1 k2 = hh_move (to_key k1) (to_key k2)
let get key = hh_get (to_key key)

let expect_stats ~nonempty ~used =
  let open SharedMem in
  let expected =
    { nonempty_slots = nonempty;
      used_slots = used;
      slots = 8;
    } in
  let {
    nonempty_slots;
    used_slots;
    slots;
  } = hash_stats () in
  let expect_equals ~name value expected =
    expect
      ~msg:(
        Printf.sprintf "Expected SharedMem.%s to equal %d, got %d"
          name expected value
      )
      (value = expected)
  in
  expect_equals ~name:"nonempty_slots" nonempty_slots expected.nonempty_slots;
  expect_equals ~name:"used_slots" used_slots expected.used_slots;
  expect_equals ~name:"slots" slots expected.slots

let expect_mem key =
  expect ~msg:(Printf.sprintf "Expected key '%s' to be in hashtable" key)
    @@ mem key

let expect_not_mem key =
  expect ~msg:(Printf.sprintf "Expected key '%s' to not be in hashtable" key)
  @@ not (mem key)

let expect_get key expected =
  let value = get key in
  expect ~msg:(
    Printf.sprintf "Expected key '%s' to have value '%s', got '%s"
      key expected value
  ) (value = expected)

let test_ops () =
  expect_stats ~nonempty:0 ~used:0;
  expect_not_mem "0";

  add "0" "";
  expect_stats ~nonempty:1 ~used:1;
  expect_mem "0";

  move "0" "1";
  expect_stats ~nonempty:2 ~used:1;
  expect_not_mem "0";
  expect_mem "1";

  remove "1";
  expect_stats ~nonempty:2 ~used:0;
  expect_not_mem "1"

let test_hashtbl_full_hh_add () =
  expect_stats ~nonempty:0 ~used:0;

  add "0" ""; add "1" ""; add "2" ""; add "3" "";
  add "4" ""; add "5" ""; add "6" ""; add "7" "";

  expect_stats ~nonempty:8 ~used:8;

  try
    add "8" "";
    expect ~msg:"Expected the hash table to be full" false
  with
    SharedMem.Hash_table_full -> ()

let test_hashtbl_full_hh_move () =
  expect_stats ~nonempty:0 ~used:0;

  add "0" ""  ; move "0" "1"; move "1" "2"; move "2" "3";
  move "3" "4"; move "4" "5"; move "5" "6"; move "6" "7";

  expect_stats ~nonempty:8 ~used:1;

  try
    move "7" "8";
    expect ~msg:"Expected the hash table to be full" false
  with
    SharedMem.Hash_table_full -> ()

(**
 * An important property to remember about the shared hash table is if a key
 * is set, it cannot be overwritten. If you want to associate a key with a new
 * value, then we need to first remove/move the key and then add the new value.
 *)
let test_no_overwrite () =
  expect_stats ~nonempty:0 ~used:0;

  add "0" "Foo";
  expect_stats ~nonempty:1 ~used:1;
  expect_mem "0";
  expect_get "0" "Foo";

  add "0" "Bar";
  expect_stats ~nonempty:1 ~used:1;
  expect_mem "0";
  expect_get "0" "Foo";

  remove "0";
  expect_stats ~nonempty:1 ~used:0;
  expect_not_mem "0";

  add "0" "Bar";
  expect_stats ~nonempty:1 ~used:1;
  expect_mem "0";
  expect_get "0" "Bar"

let test_reuse_slots () =
  expect_stats ~nonempty:0 ~used:0;

  add "0" "0";
  add "1" "1";
  expect_mem "0";
  expect_mem "1";
  expect_stats ~nonempty:2 ~used:2;

  (* If we reuse a previously used slot, the number of nonempty slots
   * stays the same
   *)
  remove "1";
  expect_not_mem "1";
  expect_stats ~nonempty:2 ~used:1;
  add "1" "Foo";
  expect_mem "1";
  expect_get "1" "Foo";
  expect_stats ~nonempty:2 ~used:2;

  (* If we move to a previously used slot, nonempty slots stays the same *)
  remove "1";
  expect_not_mem "1";
  expect_stats ~nonempty:2 ~used:1;
  move "0" "1";
  expect_not_mem "0";
  expect_mem "1";
  expect_get "1" "0";
  expect_stats ~nonempty:2 ~used:1;

  (* Moving to a brand new key will increase number of nonempty slots *)
  move "1" "2";
  expect_not_mem "1";
  expect_mem "2";
  expect_get "2" "0";
  expect_stats ~nonempty:3 ~used:1

let tests () =
  let list = [
    "test_ops", test_ops;
    "test_hashtbl_full_hh_add", test_hashtbl_full_hh_add;
    "test_hashtbl_full_hh_move", test_hashtbl_full_hh_move;
    "test_no_overwrite", test_no_overwrite;
    "test_reuse_slots", test_reuse_slots;
  ] in
  let setup_test (name, test) = name, fun () ->

  let handle = SharedMem.(
      init {
        global_size = 16;
        heap_size = 1024;
        dep_table_pow = 2;
        hash_table_pow = 3;
        shm_dirs = [];
        shm_min_avail = 0;
        log_level = 0;
      }
    ) in
    SharedMem.connect handle ~is_master:true;
    test ();
    true
  in
  List.map setup_test list

let () =
  Unit_test.run_all (tests ())
