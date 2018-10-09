
#include "gnudiff_diff.h"
#include <stdlib.h>

/* Rotate an unsigned value to the left.  */
#define ROL(v, n) ((v) << (n) | (v) >> (sizeof (v) * CHAR_BIT - (n)))

/* Given a hash value and a new character, return a new hash value.  */
#define HASH(h, c) ((c) + ROL (h, 7))

/* The type of a hash value.  */
typedef size_t hash_value;
verify (hash_value_is_unsigned, ! TYPE_SIGNED (hash_value));

/* Lines are put into equivalence classes of lines that match in lines_differ.
   Each equivalence class is represented by one of these structures,
   but only while the classes are being computed.
   Afterward, each class is represented by a number.  */
struct equivclass
{
  lin next;		/* Next item in this bucket.  */
  hash_value hash;	/* Hash of lines in this class.  */
  const QChar *line;	/* A line that fits this class.  */
  size_t length;	/* That line's length, not counting its newline.  */
};

/* Hash-table: array of buckets, each being a chain of equivalence classes.
   buckets[-1] is reserved for incomplete lines.  */
static lin *buckets;

/* Number of buckets in the hash table array, not counting buckets[-1].  */
static size_t nbuckets;

/* Array in which the equivalence classes are allocated.
   The bucket-chains go through the elements in this array.
   The number of an equivalence class is its index in this array.  */
static struct equivclass *equivs;

/* Index of first free element in the array `equivs'.  */
static lin equivs_index;

/* Number of elements allocated in the array `equivs'.  */
static lin equivs_alloc;

/* Check for binary files and compare them for exact identity.  */

/* Return 1 if BUF contains a non text character.
   SIZE is the number of characters in BUF.  */

#define binary_file_p(buf, size) (memchr (buf, 0, size) != 0)

/* Compare two lines (typically one from each input file)
   according to the command line options.
   For efficiency, this is invoked only when the lines do not match exactly
   but an option like -i might cause us to ignore the difference.
   Return nonzero if the lines differ.  */

