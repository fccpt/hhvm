<?php

// This doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from http://php.net/manual/en/class.directoryiterator.php )
 *
 * The DirectoryIterator class provides a simple interface for viewing the
 * contents of filesystem directories.
 *
 */
class DirectoryIterator extends SplFileInfo implements Traversable,
  SeekableIterator {

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/directoryiterator.construct.php
   * )
   *
   * Constructs a new directory iterator from a path.
   *
   * @path       mixed   The path of the directory to traverse.
   */
  public function __construct($path) {
    if (!hphp_directoryiterator___construct($this, $path)) {
      throw new UnexpectedValueException(
          "DirectoryIterator::__construct($path): failed to open dir");
    }
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/directoryiterator.current.php )
   *
   * Get the current DirectoryIterator item.
   *
   * @return     mixed   The current DirectoryIterator item.
   */
  public function current() {
    return hphp_directoryiterator_current($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/directoryiterator.key.php )
   *
   * Get the key for the current DirectoryIterator item.
   *
   * @return     mixed   The key for the current DirectoryIterator item.
   */
  public function key() {
    return hphp_directoryiterator_key($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/directoryiterator.next.php )
   *
   * Move forward to the next DirectoryIterator item.
   *
   * @return     mixed   No value is returned.
   */
  public function next() {
    hphp_directoryiterator_next($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/directoryiterator.rewind.php )
   *
   * Rewind the DirectoryIterator back to the start.
   *
   * @return     mixed   No value is returned.
   */
  public function rewind() {
    hphp_directoryiterator_rewind($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/directoryiterator.seek.php )
   *
   * Seek to a given position in the DirectoryIterator.
   *
   * @position   mixed   The zero-based numeric position to seek to.
   *
   * @return     mixed   No value is returned.
   */
  public function seek($position) {
    hphp_directoryiterator_seek($this, $position);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/directoryiterator.tostring.php )
   *
   * Get the file name of the current DirectoryIterator item.
   *
   * @return     mixed   Returns the file name of the current
   *                     DirectoryIterator item.
   */
  public function __toString() {
    return hphp_directoryiterator___tostring($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/directoryiterator.valid.php )
   *
   * Check whether current DirectoryIterator position is a valid file.
   *
   * @return     mixed   Returns TRUE if the position is valid, otherwise
   *                     FALSE
   */
  public function valid() {
    return hphp_directoryiterator_valid($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/directoryiterator.isdot.php )
   *
   * Determines if the current DirectoryIterator item is a directory and
   * either . or ...
   *
   * @return     mixed   TRUE if the entry is . or .., otherwise FALSE
   */
  public function isDot() {
    return hphp_directoryiterator_isdot($this);
  }
}

// This doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from http://php.net/manual/en/class.filesystemiterator.php )
 *
 * The Filesystem iterator
 *
 */
class FilesystemIterator extends DirectoryIterator
  implements SeekableIterator, Traversable, Iterator {

  const CURRENT_AS_PATHNAME = 32;
  const CURRENT_AS_FILEINFO = 0;
  const CURRENT_AS_SELF = 16;
  const CURRENT_MODE_MASK = 240;
  const KEY_AS_PATHNAME = 0;
  const KEY_AS_FILENAME = 256;
  const FOLLOW_SYMLINKS = 512;
  const KEY_MODE_MASK = 3840;
  const NEW_CURRENT_AND_KEY = 256;
  const SKIP_DOTS = 4096;
  const UNIX_PATHS = 8192;

  private $flags;

// Do NOT modifiy this doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from http://php.net/manual/en/filesystemiterator.construct.php
 * )
 *
 * Constructs a new filesystem iterator from the path.
 *
 * @path       mixed   The path of the filesystem item to be iterated over.
 * @flags      mixed   Flags may be provided which will affect the behavior
 *                     of some methods. A list of the flags can found under
 *                     FilesystemIterator predefined constants. They can
 *                     also be set later with
 *                     FilesystemIterator::setFlags()
 *
 * @return     mixed   No value is returned.
 */
  public function __construct(string $path, int $flags = null) {
    parent::__construct($path);
    if ($flags === null) {
      $flags = FilesystemIterator::KEY_AS_PATHNAME |
               FilesystemIterator::CURRENT_AS_FILEINFO |
               FilesystemIterator::SKIP_DOTS;
    }
    $this->flags = $flags;
    $this->goPastDotsIfNeeded();
  }

// Do NOT modifiy this doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from http://php.net/manual/en/filesystemiterator.current.php )
 *
 * Get file information of the current element.
 *
 * @return     mixed   The filename, file information, or $this depending
 *                     on the set flags. See the FilesystemIterator
 *                     constants.
 */
  public function current() {
    $f = parent::current();
    if ($this->flags & FilesystemIterator::CURRENT_AS_PATHNAME) {
      return $f->getPathname();
    } else if ($this->flags & FilesystemIterator::CURRENT_AS_SELF) {
      return $this;
    }
    // FilesystemIterator::CURRENT_AS_FILEINFO == 0
    return $f;
  }

// Do NOT modifiy this doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from http://php.net/manual/en/filesystemiterator.getflags.php
 * )
 *
 * Gets the handling flags, as set in FilesystemIterator::__construct() or
 * FilesystemIterator::setFlags().
 *
 * @return     mixed   The integer value of the set flags.
 */
  public function getFlags() {
    return $this->flags;
  }

// Do NOT modifiy this doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from http://php.net/manual/en/filesystemiterator.key.php )
 *
 *
 * @return     mixed   Returns the pathname or filename depending on the
 *                     set flags. See the FilesystemIterator constants.
 */
  public function key() {
    if ($this->flags & FilesystemIterator::KEY_AS_FILENAME) {
      return parent::current()->getFileName();
    }
    // FilesystemIterator::KEY_AS_PATHNAME == 0
    return parent::current()->getPathName();
  }

// Do NOT modifiy this doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from http://php.net/manual/en/filesystemiterator.next.php )
 *
 * Move to the next file.
 *
 * @return     mixed   No value is returned.
 */
  public function next() {
    parent::next();
    $this->goPastDotsIfNeeded();
  }

// Do NOT modifiy this doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from http://php.net/manual/en/filesystemiterator.rewind.php )
 *
 * Rewinds the directory back to the start.
 *
 * @return     mixed   No value is returned.
 */
  public function rewind() {
    parent::rewind();
    $this->goPastDotsIfNeeded();
  }

// Do NOT modifiy this doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from http://php.net/manual/en/filesystemiterator.setflags.php
 * )
 *
 * Sets handling flags.
 *
 * @flags      mixed   The handling flags to set. See the
 *                     FilesystemIterator constants.
 *
 * @return     mixed   No value is returned.
 */
  public function setFlags(int $flags) {
    $this->flags = $flags;
  }

  private function goPastDotsIfNeeded() {
    if ($this->flags & FilesystemIterator::SKIP_DOTS) {
      $f = parent::current();
      while ($f && $f->isDot()) {
        parent::next();
        $f = parent::current();
      }
    }
  }
}

// Do NOT modifiy this doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from
 * http://php.net/manual/en/class.recursivedirectoryiterator.php )
 *
 * The RecursiveDirectoryIterator provides an interface for iterating
 * recursively over filesystem directories.
 *
 */
class RecursiveDirectoryIterator extends FilesystemIterator
  implements RecursiveIterator {

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursivedirectoryiterator.construct.php )
   *
   * Constructs a RecursiveDirectoryIterator() for the provided path.
   *
   * @path       mixed   The path of the directory to be iterated over.
   * @flags      mixed   Flags may be provided which will affect the behavior
   *                     of some methods. A list of the flags can found under
   *                     FilesystemIterator predefined constants. They can
   *                     also be set later with
   *                     FilesystemIterator::setFlags().
   *
   * @return     mixed   Returns the newly created
   *                     RecursiveDirectoryIterator.
   */
  function __construct($path, $flags = null) {
    if ($flags === null) {
      $flags = FilesystemIterator::KEY_AS_PATHNAME |
               FilesystemIterator::CURRENT_AS_FILEINFO;
    }
    if (!hphp_recursivedirectoryiterator___construct($this, $path, $flags)) {
      throw new UnexpectedValueException(
          "RecursiveDirectoryIterator::__construct($path): failed to open dir");
    }
  }

  function current() {
    return hphp_recursivedirectoryiterator_current($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursivedirectoryiterator.key.php )
   *
   *
   * @return     mixed   The path and filename of the current dir entry.
   */
  function key() {
    return hphp_recursivedirectoryiterator_key($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursivedirectoryiterator.next.php )
   *
   *
   * @return     mixed   No value is returned.
   */
  public function next() {
    hphp_recursivedirectoryiterator_next($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursivedirectoryiterator.rewind.php )
   *
   *
   * @return     mixed   No value is returned.
   */
  public function rewind() {
    hphp_recursivedirectoryiterator_rewind($this);
  }

  public function seek($position) {
    hphp_recursivedirectoryiterator_seek($this, $position);
  }

  public function __toString() {
    return hphp_recursivedirectoryiterator___toString($this);
  }

  public function valid() {
    return hphp_recursivedirectoryiterator_valid($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursivedirectoryiterator.haschildren.php )
   *
   *
   * @return     mixed   Returns whether the current entry is a directory,
   *                     but not '.' or '..'
   */
  function hasChildren() {
    return hphp_recursivedirectoryiterator_haschildren($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursivedirectoryiterator.getchildren.php )
   *
   *
   * @return     mixed   The filename, file information, or $this depending
   *                     on the set flags. See the FilesystemIterator
   *                     constants.
   */
  function getChildren() {
    return hphp_recursivedirectoryiterator_getchildren($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursivedirectoryiterator.getsubpath.php )
   *
   * Gets the sub path. WarningThis function is currently not documented;
   * only its argument list is available.
   *
   * @return     mixed   The sub path (sub directory).
   */
  function getSubPath() {
    return hphp_recursivedirectoryiterator_getsubpath($this);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from
   * http://php.net/manual/en/recursivedirectoryiterator.getsubpathname.php )
   *
   * Gets the sub path and filename. WarningThis function is currently not
   * documented; only its argument list is available.
   *
   * @return     mixed   The sub path (sub directory) and filename.
   */
  function getSubPathname() {
    return hphp_recursivedirectoryiterator_getsubpathname($this);
  }
}
