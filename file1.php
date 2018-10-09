#include "gnudiff_diff.h"
#include <stdlib.h>
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
