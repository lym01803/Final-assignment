<?php
namespace Wamania\Snowball;
/**
 * English Porter 2
 *
 * @link http://snowball.tartarus.org/algorithms/english/stemmer.html
 * @author wamania
 *
*/
class Utf8
{
    /**
     * UTF-8 lookup table for lower case accented letters
     *
     * This lookuptable defines replacements for accented characters from the ASCII-7
     * range. This are lower case letters only.
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    utf8_deaccent()
     */
    private static $utf8_lower_accents = array(
        'à' => 'a', 'ô' => 'o', 'd' => 'd', '?' => 'f', 'ë' => 'e', 'š' => 's', 'o' => 'o',
        'ß' => 'ss', 'a' => 'a', 'r' => 'r', '?' => 't', 'n' => 'n', 'a' => 'a', 'k' => 'k',
        's' => 's', '?' => 'y', 'n' => 'n', 'l' => 'l', 'h' => 'h', '?' => 'p', 'ó' => 'o',
        'ú' => 'u', 'e' => 'e', 'é' => 'e', 'ç' => 'c', '?' => 'w', 'c' => 'c', 'õ' => 'o',
        '?' => 's', 'ø' => 'o', 'g' => 'g', 't' => 't', '?' => 's', 'e' => 'e', 'c' => 'c',
        's' => 's', 'î' => 'i', 'u' => 'u', 'c' => 'c', 'e' => 'e', 'w' => 'w', '?' => 't',
        'u' => 'u', 'c' => 'c', 'ö' => 'oe', 'è' => 'e', 'y' => 'y', 'a' => 'a', 'l' => 'l',
        'u' => 'u', 'u' => 'u', 's' => 's', 'g' => 'g', 'l' => 'l', 'ƒ' => 'f', 'ž' => 'z',
        '?' => 'w', '?' => 'b', 'å' => 'a', 'ì' => 'i', 'ï' => 'i', '?' => 'd', 't' => 't',
        'r' => 'r', 'ä' => 'ae', 'í' => 'i', 'r' => 'r', 'ê' => 'e', 'ü' => 'ue', 'ò' => 'o',
        'e' => 'e', 'ñ' => 'n', 'n' => 'n', 'h' => 'h', 'g' => 'g', 'd' => 'd', 'j' => 'j',
        'ÿ' => 'y', 'u' => 'u', 'u' => 'u', 'u' => 'u', 't' => 't', 'ý' => 'y', 'o' => 'o',
        'â' => 'a', 'l' => 'l', '?' => 'w', 'z' => 'z', 'i' => 'i', 'ã' => 'a', 'g' => 'g',
        '?' => 'm', 'o' => 'o', 'i' => 'i', 'ù' => 'u', 'i' => 'i', 'z' => 'z', 'á' => 'a',
        'û' => 'u', 'þ' => 'th', 'ð' => 'dh', 'æ' => 'ae', 'µ' => 'u',
    );
    /**
     * UTF-8 Case lookup table
     *
     * This lookuptable defines the upper case letters to their correspponding
     * lower case letter in UTF-8
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    private static $utf8_lower_to_upper = array(
        0x0061=>0x0041, 0x03C6=>0x03A6, 0x0163=>0x0162, 0x00E5=>0x00C5, 0x0062=>0x0042,
        0x013A=>0x0139, 0x00E1=>0x00C1, 0x0142=>0x0141, 0x03CD=>0x038E, 0x0101=>0x0100,
        0x0491=>0x0490, 0x03B4=>0x0394, 0x015B=>0x015A, 0x0064=>0x0044, 0x03B3=>0x0393,
        0x00F4=>0x00D4, 0x044A=>0x042A, 0x0439=>0x0419, 0x0113=>0x0112, 0x043C=>0x041C,
        0x015F=>0x015E, 0x0144=>0x0143, 0x00EE=>0x00CE, 0x045E=>0x040E, 0x044F=>0x042F,
        0x03BA=>0x039A, 0x0155=>0x0154, 0x0069=>0x0049, 0x0073=>0x0053, 0x1E1F=>0x1E1E,
        0x0135=>0x0134, 0x0447=>0x0427, 0x03C0=>0x03A0, 0x0438=>0x0418, 0x00F3=>0x00D3,
        0x0440=>0x0420, 0x0454=>0x0404, 0x0435=>0x0415, 0x0449=>0x0429, 0x014B=>0x014A,
        0x0431=>0x0411, 0x0459=>0x0409, 0x1E03=>0x1E02, 0x00F6=>0x00D6, 0x00F9=>0x00D9,
        0x006E=>0x004E, 0x0451=>0x0401, 0x03C4=>0x03A4, 0x0443=>0x0423, 0x015D=>0x015C,
        0x0453=>0x0403, 0x03C8=>0x03A8, 0x0159=>0x0158, 0x0067=>0x0047, 0x00E4=>0x00C4,
        0x03AC=>0x0386, 0x03AE=>0x0389, 0x0167=>0x0166, 0x03BE=>0x039E, 0x0165=>0x0164,
        0x0117=>0x0116, 0x0109=>0x0108, 0x0076=>0x0056, 0x00FE=>0x00DE, 0x0157=>0x0156,
        0x00FA=>0x00DA, 0x1E61=>0x1E60, 0x1E83=>0x1E82, 0x00E2=>0x00C2, 0x0119=>0x0118,
        0x0146=>0x0145, 0x0070=>0x0050, 0x0151=>0x0150, 0x044E=>0x042E, 0x0129=>0x0128,
        0x03C7=>0x03A7, 0x013E=>0x013D, 0x0442=>0x0422, 0x007A=>0x005A, 0x0448=>0x0428,
        0x03C1=>0x03A1, 0x1E81=>0x1E80, 0x016D=>0x016C, 0x00F5=>0x00D5, 0x0075=>0x0055,
        0x0177=>0x0176, 0x00FC=>0x00DC, 0x1E57=>0x1E56, 0x03C3=>0x03A3, 0x043A=>0x041A,
        0x006D=>0x004D, 0x016B=>0x016A, 0x0171=>0x0170, 0x0444=>0x0424, 0x00EC=>0x00CC,
        0x0169=>0x0168, 0x03BF=>0x039F, 0x006B=>0x004B, 0x00F2=>0x00D2, 0x00E0=>0x00C0,
        0x0434=>0x0414, 0x03C9=>0x03A9, 0x1E6B=>0x1E6A, 0x00E3=>0x00C3, 0x044D=>0x042D,
        0x0436=>0x0416, 0x01A1=>0x01A0, 0x010D=>0x010C, 0x011D=>0x011C, 0x00F0=>0x00D0,
        0x013C=>0x013B, 0x045F=>0x040F, 0x045A=>0x040A, 0x00E8=>0x00C8, 0x03C5=>0x03A5,
        0x0066=>0x0046, 0x00FD=>0x00DD, 0x0063=>0x0043, 0x021B=>0x021A, 0x00EA=>0x00CA,
        0x03B9=>0x0399, 0x017A=>0x0179, 0x00EF=>0x00CF, 0x01B0=>0x01AF, 0x0065=>0x0045,
        0x03BB=>0x039B, 0x03B8=>0x0398, 0x03BC=>0x039C, 0x045C=>0x040C, 0x043F=>0x041F,
        0x044C=>0x042C, 0x00FE=>0x00DE, 0x00F0=>0x00D0, 0x1EF3=>0x1EF2, 0x0068=>0x0048,
        0x00EB=>0x00CB, 0x0111=>0x0110, 0x0433=>0x0413, 0x012F=>0x012E, 0x00E6=>0x00C6,
        0x0078=>0x0058, 0x0161=>0x0160, 0x016F=>0x016E, 0x03B1=>0x0391, 0x0457=>0x0407,
        0x0173=>0x0172, 0x00FF=>0x0178, 0x006F=>0x004F, 0x043B=>0x041B, 0x03B5=>0x0395,
        0x0445=>0x0425, 0x0121=>0x0120, 0x017E=>0x017D, 0x017C=>0x017B, 0x03B6=>0x0396,
        0x03B2=>0x0392, 0x03AD=>0x0388, 0x1E85=>0x1E84, 0x0175=>0x0174, 0x0071=>0x0051,
        0x0437=>0x0417, 0x1E0B=>0x1E0A, 0x0148=>0x0147, 0x0105=>0x0104, 0x0458=>0x0408,
        0x014D=>0x014C, 0x00ED=>0x00CD, 0x0079=>0x0059, 0x010B=>0x010A, 0x03CE=>0x038F,
        0x0072=>0x0052, 0x0430=>0x0410, 0x0455=>0x0405, 0x0452=>0x0402, 0x0127=>0x0126,
        0x0137=>0x0136, 0x012B=>0x012A, 0x03AF=>0x038A, 0x044B=>0x042B, 0x006C=>0x004C,
        0x03B7=>0x0397, 0x0125=>0x0124, 0x0219=>0x0218, 0x00FB=>0x00DB, 0x011F=>0x011E,
        0x043E=>0x041E, 0x1E41=>0x1E40, 0x03BD=>0x039D, 0x0107=>0x0106, 0x03CB=>0x03AB,
        0x0446=>0x0426, 0x00FE=>0x00DE, 0x00E7=>0x00C7, 0x03CA=>0x03AA, 0x0441=>0x0421,
        0x0432=>0x0412, 0x010F=>0x010E, 0x00F8=>0x00D8, 0x0077=>0x0057, 0x011B=>0x011A,
        0x0074=>0x0054, 0x006A=>0x004A, 0x045B=>0x040B, 0x0456=>0x0406, 0x0103=>0x0102,
        0x03BB=>0x039B, 0x00F1=>0x00D1, 0x043D=>0x041D, 0x03CC=>0x038C, 0x00E9=>0x00C9,
        0x00F0=>0x00D0, 0x0457=>0x0407, 0x0123=>0x0122,
    );
    /**
     * UTF-8 Case lookup table
     *
     * This lookuptable defines the lower case letters to their correspponding
     * upper case letter in UTF-8 (it does so by flipping $utf8_lower_to_upper)
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    //private static $utf8_upper_to_lower = array_flip(self::$utf8_lower_to_upper);
    /**
     * UTF-8 lookup table for upper case accented letters
     *
     * This lookuptable defines replacements for accented characters from the ASCII-7
     * range. This are upper case letters only.
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    utf8_deaccent()
     */
    private static $utf8_upper_accents = array(
        'À' => 'A', 'Ô' => 'O', 'D' => 'D', '?' => 'F', 'Ë' => 'E', 'Š' => 'S', 'O' => 'O',
        'A' => 'A', 'R' => 'R', '?' => 'T', 'N' => 'N', 'A' => 'A', 'K' => 'K',
        'S' => 'S', '?' => 'Y', 'N' => 'N', 'L' => 'L', 'H' => 'H', '?' => 'P', 'Ó' => 'O',
        'Ú' => 'U', 'E' => 'E', 'É' => 'E', 'Ç' => 'C', '?' => 'W', 'C' => 'C', 'Õ' => 'O',
        '?' => 'S', 'Ø' => 'O', 'G' => 'G', 'T' => 'T', '?' => 'S', 'E' => 'E', 'C' => 'C',
        'S' => 'S', 'Î' => 'I', 'U' => 'U', 'C' => 'C', 'E' => 'E', 'W' => 'W', '?' => 'T',
        'U' => 'U', 'C' => 'C', 'Ö' => 'Oe', 'È' => 'E', 'Y' => 'Y', 'A' => 'A', 'L' => 'L',
        'U' => 'U', 'U' => 'U', 'S' => 'S', 'G' => 'G', 'L' => 'L', 'ƒ' => 'F', 'Ž' => 'Z',
        '?' => 'W', '?' => 'B', 'Å' => 'A', 'Ì' => 'I', 'Ï' => 'I', '?' => 'D', 'T' => 'T',
        'R' => 'R', 'Ä' => 'Ae', 'Í' => 'I', 'R' => 'R', 'Ê' => 'E', 'Ü' => 'Ue', 'Ò' => 'O',
        'E' => 'E', 'Ñ' => 'N', 'N' => 'N', 'H' => 'H', 'G' => 'G', 'Ð' => 'D', 'J' => 'J',
        'Ÿ' => 'Y', 'U' => 'U', 'U' => 'U', 'U' => 'U', 'T' => 'T', 'Ý' => 'Y', 'O' => 'O',
        'Â' => 'A', 'L' => 'L', '?' => 'W', 'Z' => 'Z', 'I' => 'I', 'Ã' => 'A', 'G' => 'G',
        '?' => 'M', 'O' => 'O', 'I' => 'I', 'Ù' => 'U', 'I' => 'I', 'Z' => 'Z', 'Á' => 'A',
        'Û' => 'U', 'Þ' => 'Th', 'Ð' => 'Dh', 'Æ' => 'Ae',
    );
    /**
     * UTF-8 array of common special characters
     *
     * This array should contain all special characters (not a letter or digit)
     * defined in the various local charsets - it's not a complete list of non-alphanum
     * characters in UTF-8. It's not perfect but should match most cases of special
     * chars.
     *
     * The controlchars 0x00 to 0x19 are _not_ included in this array. The space 0x20 is!
     * These chars are _not_ in the array either:  _ (0x5f), : 0x3a, . 0x2e, - 0x2d, * 0x2a
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    utf8_stripspecials()
     */
    private static $utf8_special_chars = array(
        0x001a, 0x001b, 0x001c, 0x001d, 0x001e, 0x001f, 0x0020, 0x0021, 0x0022, 0x0023,
        0x0024, 0x0025, 0x0026, 0x0027, 0x0028, 0x0029,         0x002b, 0x002c,
        0x002f,         0x003b, 0x003c, 0x003d, 0x003e, 0x003f, 0x0040, 0x005b,
        0x005c, 0x005d, 0x005e,         0x0060, 0x007b, 0x007c, 0x007d, 0x007e,
        0x007f, 0x0080, 0x0081, 0x0082, 0x0083, 0x0084, 0x0085, 0x0086, 0x0087, 0x0088,
        0x0089, 0x008a, 0x008b, 0x008c, 0x008d, 0x008e, 0x008f, 0x0090, 0x0091, 0x0092,
        0x0093, 0x0094, 0x0095, 0x0096, 0x0097, 0x0098, 0x0099, 0x009a, 0x009b, 0x009c,
        0x009d, 0x009e, 0x009f, 0x00a0, 0x00a1, 0x00a2, 0x00a3, 0x00a4, 0x00a5, 0x00a6,
        0x00a7, 0x00a8, 0x00a9, 0x00aa, 0x00ab, 0x00ac, 0x00ad, 0x00ae, 0x00af, 0x00b0,
        0x00b1, 0x00b2, 0x00b3, 0x00b4, 0x00b5, 0x00b6, 0x00b7, 0x00b8, 0x00b9, 0x00ba,
        0x00bb, 0x00bc, 0x00bd, 0x00be, 0x00bf, 0x00d7, 0x00f7, 0x02c7, 0x02d8, 0x02d9,
        0x02da, 0x02db, 0x02dc, 0x02dd, 0x0300, 0x0301, 0x0303, 0x0309, 0x0323, 0x0384,
        0x0385, 0x0387, 0x03b2, 0x03c6, 0x03d1, 0x03d2, 0x03d5, 0x03d6, 0x05b0, 0x05b1,
        0x05b2, 0x05b3, 0x05b4, 0x05b5, 0x05b6, 0x05b7, 0x05b8, 0x05b9, 0x05bb, 0x05bc,
        0x05bd, 0x05be, 0x05bf, 0x05c0, 0x05c1, 0x05c2, 0x05c3, 0x05f3, 0x05f4, 0x060c,
        0x061b, 0x061f, 0x0640, 0x064b, 0x064c, 0x064d, 0x064e, 0x064f, 0x0650, 0x0651,
        0x0652, 0x066a, 0x0e3f, 0x200c, 0x200d, 0x200e, 0x200f, 0x2013, 0x2014, 0x2015,
        0x2017, 0x2018, 0x2019, 0x201a, 0x201c, 0x201d, 0x201e, 0x2020, 0x2021, 0x2022,
        0x2026, 0x2030, 0x2032, 0x2033, 0x2039, 0x203a, 0x2044, 0x20a7, 0x20aa, 0x20ab,
        0x20ac, 0x2116, 0x2118, 0x2122, 0x2126, 0x2135, 0x2190, 0x2191, 0x2192, 0x2193,
        0x2194, 0x2195, 0x21b5, 0x21d0, 0x21d1, 0x21d2, 0x21d3, 0x21d4, 0x2200, 0x2202,
        0x2203, 0x2205, 0x2206, 0x2207, 0x2208, 0x2209, 0x220b, 0x220f, 0x2211, 0x2212,
        0x2215, 0x2217, 0x2219, 0x221a, 0x221d, 0x221e, 0x2220, 0x2227, 0x2228, 0x2229,
        0x222a, 0x222b, 0x2234, 0x223c, 0x2245, 0x2248, 0x2260, 0x2261, 0x2264, 0x2265,
        0x2282, 0x2283, 0x2284, 0x2286, 0x2287, 0x2295, 0x2297, 0x22a5, 0x22c5, 0x2310,
        0x2320, 0x2321, 0x2329, 0x232a, 0x2469, 0x2500, 0x2502, 0x250c, 0x2510, 0x2514,
        0x2518, 0x251c, 0x2524, 0x252c, 0x2534, 0x253c, 0x2550, 0x2551, 0x2552, 0x2553,
        0x2554, 0x2555, 0x2556, 0x2557, 0x2558, 0x2559, 0x255a, 0x255b, 0x255c, 0x255d,
        0x255e, 0x255f, 0x2560, 0x2561, 0x2562, 0x2563, 0x2564, 0x2565, 0x2566, 0x2567,
        0x2568, 0x2569, 0x256a, 0x256b, 0x256c, 0x2580, 0x2584, 0x2588, 0x258c, 0x2590,
        0x2591, 0x2592, 0x2593, 0x25a0, 0x25b2, 0x25bc, 0x25c6, 0x25ca, 0x25cf, 0x25d7,
        0x2605, 0x260e, 0x261b, 0x261e, 0x2660, 0x2663, 0x2665, 0x2666, 0x2701, 0x2702,
        0x2703, 0x2704, 0x2706, 0x2707, 0x2708, 0x2709, 0x270c, 0x270d, 0x270e, 0x270f,
        0x2710, 0x2711, 0x2712, 0x2713, 0x2714, 0x2715, 0x2716, 0x2717, 0x2718, 0x2719,
        0x271a, 0x271b, 0x271c, 0x271d, 0x271e, 0x271f, 0x2720, 0x2721, 0x2722, 0x2723,
        0x2724, 0x2725, 0x2726, 0x2727, 0x2729, 0x272a, 0x272b, 0x272c, 0x272d, 0x272e,
        0x272f, 0x2730, 0x2731, 0x2732, 0x2733, 0x2734, 0x2735, 0x2736, 0x2737, 0x2738,
        0x2739, 0x273a, 0x273b, 0x273c, 0x273d, 0x273e, 0x273f, 0x2740, 0x2741, 0x2742,
        0x2743, 0x2744, 0x2745, 0x2746, 0x2747, 0x2748, 0x2749, 0x274a, 0x274b, 0x274d,
        0x274f, 0x2750, 0x2751, 0x2752, 0x2756, 0x2758, 0x2759, 0x275a, 0x275b, 0x275c,
        0x275d, 0x275e, 0x2761, 0x2762, 0x2763, 0x2764, 0x2765, 0x2766, 0x2767, 0x277f,
        0x2789, 0x2793, 0x2794, 0x2798, 0x2799, 0x279a, 0x279b, 0x279c, 0x279d, 0x279e,
        0x279f, 0x27a0, 0x27a1, 0x27a2, 0x27a3, 0x27a4, 0x27a5, 0x27a6, 0x27a7, 0x27a8,
        0x27a9, 0x27aa, 0x27ab, 0x27ac, 0x27ad, 0x27ae, 0x27af, 0x27b1, 0x27b2, 0x27b3,
        0x27b4, 0x27b5, 0x27b6, 0x27b7, 0x27b8, 0x27b9, 0x27ba, 0x27bb, 0x27bc, 0x27bd,
        0x27be, 0xf6d9, 0xf6da, 0xf6db, 0xf8d7, 0xf8d8, 0xf8d9, 0xf8da, 0xf8db, 0xf8dc,
        0xf8dd, 0xf8de, 0xf8df, 0xf8e0, 0xf8e1, 0xf8e2, 0xf8e3, 0xf8e4, 0xf8e5, 0xf8e6,
        0xf8e7, 0xf8e8, 0xf8e9, 0xf8ea, 0xf8eb, 0xf8ec, 0xf8ed, 0xf8ee, 0xf8ef, 0xf8f0,
        0xf8f1, 0xf8f2, 0xf8f3, 0xf8f4, 0xf8f5, 0xf8f6, 0xf8f7, 0xf8f8, 0xf8f9, 0xf8fa,
        0xf8fb, 0xf8fc, 0xf8fd, 0xf8fe, 0xfe7c, 0xfe7d,
    );
    /**
     * URL-Encode a filename to allow unicodecharacters
     *
     * Slashes are not encoded
     *
     * When the second parameter is true the string will
     * be encoded only if non ASCII characters are detected -
     * This makes it safe to run it multiple times on the
     * same string (default is true)
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    urlencode
     */
    public static function encode_fn($file,$safe=true)
    {
        if($safe && preg_match('#^[a-zA-Z0-9/_\-.%]+$#',$file)){
            return $file;
        }
        $file = urlencode($file);
        $file = str_replace('%2F','/',$file);
        return $file;
    }
    /**
     * URL-Decode a filename
     *
     * This is just a wrapper around urldecode
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    urldecode
     */
    public static function decode_fn($file)
    {
        $file = urldecode($file);
        return $file;
    }
    /**
     * Checks if a string contains 7bit ASCII only
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public static function is_ascii($str)
    {
        for($i=0; $i<strlen($str); $i++){
            if(ord($str{$i}) >127) return false;
        }
        return true;
    }
    /**
     * Strips all highbyte chars
     *
     * Returns a pure ASCII7 string
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public static function strip($str)
    {
        $ascii = '';
        for($i=0; $i<strlen($str); $i++){
            if(ord($str{$i}) <128){
                $ascii .= $str{$i};
            }
        }
        return $ascii;
    }
    /**
     * Tries to detect if a string is in Unicode encoding
     *
     * @author <bmorel@ssi.fr>
     * @link   http://www.php.net/manual/en/function.utf8-encode.php
     */
    public static function check($str)
    {
        for ($i=0; $i<strlen($str); $i++) {
            if (ord($str[$i]) < 0x80) continue; # 0bbbbbbb
            elseif ((ord($str[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
            elseif ((ord($str[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
            elseif ((ord($str[$i]) & 0xF8) == 0xF0) $n=3; # 11110bbb
            elseif ((ord($str[$i]) & 0xFC) == 0xF8) $n=4; # 111110bb
            elseif ((ord($str[$i]) & 0xFE) == 0xFC) $n=5; # 1111110b
            else return false; # Does not match any model
            for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
                if ((++$i == strlen($str)) || ((ord($str[$i]) & 0xC0) != 0x80))
                return false;
            }
        }
        return true;
    }
    /**
     * Unicode aware replacement for strlen()
     *
     * utf8_decode() converts characters that are not in ISO-8859-1
     * to '?', which, for the purpose of counting, is alright - It's
     * even faster than mb_strlen.
     *
     * @author <chernyshevsky at hotmail dot com>
     * @see    strlen()
     * @see    utf8_decode()
     */
    public static function strlen($string)
    {
        return strlen(utf8_decode($string));
    }
    /**
     * Unicode aware replacement for substr()
     *
     * @author lmak at NOSPAM dot iti dot gr
     * @link   http://www.php.net/manual/en/function.substr.php
     * @see    substr()
     */
    public static function substr($str,$start,$length=null)
    {
        $ar = array();
        preg_match_all("/./u", $str, $ar);
        if($length != null) {
            return join("",array_slice($ar[0],$start,$length));
        } else {
            return join("",array_slice($ar[0],$start));
        }
    }
    /**
     * Unicode aware replacement for substr_replace()
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    substr_replace()
     */
    public static function substr_replace($string, $replacement, $start , $length=null )
    {
        $ret = '';
        if($start>0) $ret .= self::substr($string, 0, $start);
        $ret .= $replacement;
        if($length!=null) $ret .= self::substr($string, $start+$length);
        return $ret;
    }
    /**
     * Unicode aware replacement for explode
     *
     * @TODO   support third limit arg
     * @author Harry Fuecks <hfuecks@gmail.com>
     * @see    explode();
     */
    public static function explode($sep, $str)
    {
        if ( $sep == '' ) {
            trigger_error('Empty delimiter',E_USER_WARNING);
            return FALSE;
        }
        return preg_split('!'.preg_quote($sep,'!').'!u',$str);
    }
    /**
     * Unicode aware replacement for strrepalce()
     *
     * @todo   support PHP5 count (fourth arg)
     * @author Harry Fuecks <hfuecks@gmail.com>
     * @see    strreplace();
     */
    public static function str_replace($s,$r,$str)
    {
        if(!is_array($s)){
            $s = '!'.preg_quote($s,'!').'!u';
        }else{
            foreach ($s as $k => $v) {
                $s[$k] = '!'.preg_quote($v).'!u';
            }
        }
        return preg_replace($s,$r,$str);
    }
    /**
     * Unicode aware replacement for ltrim()
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    ltrim()
     * @return string
     */
    public static function ltrim($str,$charlist='')
    {
        if($charlist == '') return ltrim($str);
        //quote charlist for use in a characterclass
        $charlist = preg_replace('!([\\\\\\-\\]\\[/])!','\\\${1}',$charlist);
        return preg_replace('/^['.$charlist.']+/u','',$str);
    }
    /**
     * Unicode aware replacement for rtrim()
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    rtrim()
     * @return string
     */
    public static function  rtrim($str,$charlist='')
    {
        if($charlist == '') return rtrim($str);
        //quote charlist for use in a characterclass
        $charlist = preg_replace('!([\\\\\\-\\]\\[/])!','\\\${1}',$charlist);
        return preg_replace('/['.$charlist.']+$/u','',$str);
    }
    /**
     * Unicode aware replacement for trim()
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    trim()
     * @return string
     */
    public static function  trim($str,$charlist='')
    {
        if($charlist == '') return trim($str);
        return self::ltrim(self::rtrim($str));
    }
    /**
     * This is a unicode aware replacement for strtolower()
     *
     * Uses mb_string extension if available
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    strtolower()
     * @see    utf8_strtoupper()
     */
    public static function strtolower($string)
    {
        if(!defined('UTF8_NOMBSTRING') && function_exists('mb_strtolower'))
            return mb_strtolower($string,'utf-8');
        //global $utf8_upper_to_lower;
        $utf8_upper_to_lower = array_flip(self::$utf8_lower_to_upper);
        $uni = self::utf8_to_unicode($string);
        $cnt = count($uni);
        for ($i=0; $i < $cnt; $i++){
            if($utf8_upper_to_lower[$uni[$i]]){
                $uni[$i] = $utf8_upper_to_lower[$uni[$i]];
            }
        }
        return self::unicode_to_utf8($uni);
    }
    /**
     * This is a unicode aware replacement for strtoupper()
     *
     * Uses mb_string extension if available
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    strtoupper()
     * @see    utf8_strtoupper()
     */
    public static function strtoupper($string)
    {
        if(!defined('UTF8_NOMBSTRING') && function_exists('mb_strtolower'))
            return mb_strtoupper($string,'utf-8');
        //global $utf8_lower_to_upper;
        $uni = self::utf8_to_unicode($string);
        $cnt = count($uni);
        for ($i=0; $i < $cnt; $i++){
            if(self::$utf8_lower_to_upper[$uni[$i]]){
                $uni[$i] = self::$utf8_lower_to_upper[$uni[$i]];
            }
        }
        return self::unicode_to_utf8($uni);
    }
    /**
     * Replace accented UTF-8 characters by unaccented ASCII-7 equivalents
     *
     * Use the optional parameter to just deaccent lower ($case = -1) or upper ($case = 1)
     * letters. Default is to deaccent both cases ($case = 0)
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public static function deaccent($string,$case=0)
    {
        if($case <= 0){
            //global $utf8_lower_accents;
            $string = str_replace(array_keys(self::$utf8_lower_accents),array_values(self::$utf8_lower_accents),$string);
        }
        if($case >= 0){
            //global $utf8_upper_accents;
            $string = str_replace(array_keys(self::$utf8_upper_accents),array_values(self::$utf8_upper_accents),$string);
        }
        return $string;
    }
    /**
     * Removes special characters (nonalphanumeric) from a UTF-8 string
     *
     * This function adds the controlchars 0x00 to 0x19 to the array of
     * stripped chars (they are not included in $utf8_special_chars)
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @param  string $string     The UTF8 string to strip of special chars
     * @param  string $repl       Replace special with this string
     * @param  string $additional Additional chars to strip (used in regexp char class)
     */
    public static function stripspecials($string,$repl='',$additional='')
    {
        //global $utf8_special_chars;
        static $specials = null;
        if(is_null($specials)){
            $specials = preg_quote(self::unicode_to_utf8(self::$utf8_special_chars), '/');
        }
        return preg_replace('/['.$additional.'\x00-\x19'.$specials.']/u',$repl,$string);
    }
    /**
     * This is an Unicode aware replacement for strpos
     *
     * Uses mb_string extension if available
     *
     * @author Harry Fuecks <hfuecks@gmail.com>
     * @see    strpos()
     */
    public static function strpos($haystack, $needle, $offset=0)
    {
        if(!defined('UTF8_NOMBSTRING') && function_exists('mb_strpos'))
            return mb_strpos($haystack,$needle,$offset,'utf-8');
        if(!$offset){
            $ar = self::explode($needle, $haystack);
            if ( count($ar) > 1 ) {
                return self::strlen($ar[0]);
            }
            return false;
        } else {
            if ( !is_int($offset) ) {
                trigger_error('Offset must be an integer',E_USER_WARNING);
                return false;
            }
            $str = self::substr($haystack, $offset);
            if ( false !== ($pos = self::strpos($str, $needle))){
                return $pos + $offset;
            }
            return false;
        }
    }
    /**
     * This is an Unicode aware replacement for strrpos
     *
     * Uses mb_string extension if available
     *
     * @author Harry Fuecks <hfuecks@gmail.com>
     * @see    strpos()
     */
    public static function strrpos($haystack, $needle, $offset=0)
    {
        if(!defined('UTF8_NOMBSTRING') && function_exists('mb_strrpos'))
            return mb_strrpos($haystack, $needle, $offset, 'utf-8');
        if (!$offset) {
            $ar = self::explode($needle, $haystack);
            $count = count($ar);
            if ( $count > 1 ) {
                return self::strlen($haystack) - self::strlen($ar[($count-1)]) - self::strlen($needle);
            }
            return false;
        } else {
            if ( !is_int($offset) ) {
                trigger_error('Offset must be an integer', E_USER_WARNING);
                return false;
            }
            $str = self::substr($haystack, $offset);
            if ( false !== ($pos = self::strrpos($str, $needle))){
                return $pos + $offset;
            }
            return false;
        }
    }
    /**
     * Encodes UTF-8 characters to HTML entities
     *
     * @author <vpribish at shopping dot com>
     * @link   http://www.php.net/manual/en/function.utf8-decode.php
     */
    public static function tohtml ($str)
    {
        $ret = '';
        $max = strlen($str);
        $last = 0;  // keeps the index of the last regular character
        for ($i=0; $i<$max; $i++) {
            $c = $str{$i};
            $c1 = ord($c);
            if ($c1>>5 == 6) {  // 110x xxxx, 110 prefix for 2 bytes unicode
                $ret .= substr($str, $last, $i-$last); // append all the regular characters we've passed
                $c1 &= 31; // remove the 3 bit two bytes prefix
                $c2 = ord($str{++$i}); // the next byte
                $c2 &= 63;  // remove the 2 bit trailing byte prefix
                $c2 |= (($c1 & 3) << 6); // last 2 bits of c1 become first 2 of c2
                $c1 >>= 2; // c1 shifts 2 to the right
                $ret .= '&#' . ($c1 * 100 + $c2) . ';'; // this is the fastest string concatenation
                $last = $i+1;
            }
        }
        return $ret . substr($str, $last, $i); // append the last batch of regular characters
    }
    /**
     * This function returns any UTF-8 encoded text as a list of
     * Unicode values:
     *
     * @author Scott Michael Reynen <scott@randomchaos.com>
     * @link   http://www.randomchaos.com/document.php?source=php_and_unicode
     * @see    unicode_to_utf8()
     */
    public static function utf8_to_unicode( &$str )
    {
        $unicode = array();
        $values = array();
        $looking_for = 1;
        for ($i = 0; $i < strlen( $str ); $i++ ) {
            $this_value = ord( $str[ $i ] );
            if ( $this_value < 128 ) $unicode[] = $this_value;
            else {
                if ( count( $values ) == 0 ) $looking_for = ( $this_value < 224 ) ? 2 : 3;
                $values[] = $this_value;
                if ( count( $values ) == $looking_for ) {
                    $number = ( $looking_for == 3 ) ?
                    ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                    ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
                    $unicode[] = $number;
                    $values = array();
                    $looking_for = 1;
                }
            }
        }
        return $unicode;
    }
    /**
     * This function converts a Unicode array back to its UTF-8 representation
     *
     * @author Scott Michael Reynen <scott@randomchaos.com>
     * @link   http://www.randomchaos.com/document.php?source=php_and_unicode
     * @see    utf8_to_unicode()
     */
    public static function unicode_to_utf8( &$str )
    {
        if (!is_array($str)) return '';
        $utf8 = '';
        foreach( $str as $unicode ) {
            if ( $unicode < 128 ) {
                $utf8.= chr( $unicode );
            } elseif ( $unicode < 2048 ) {
                $utf8.= chr( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
                $utf8.= chr( 128 + ( $unicode % 64 ) );
            } else {
                $utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
                $utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
                $utf8.= chr( 128 + ( $unicode % 64 ) );
            }
        }
        return $utf8;
    }
    /**
     * UTF-8 to UTF-16BE conversion.
     *
     * Maybe really UCS-2 without mb_string due to utf8_to_unicode limits
     */
    public static function utf8_to_utf16be(&$str, $bom = false)
    {
        $out = $bom ? "\xFE\xFF" : '';
        if(!defined('UTF8_NOMBSTRING') && function_exists('mb_convert_encoding'))
            return $out.mb_convert_encoding($str,'UTF-16BE','UTF-8');
        $uni = self::utf8_to_unicode($str);
        foreach($uni as $cp){
            $out .= pack('n',$cp);
        }
        return $out;
    }
    /**
     * UTF-8 to UTF-16BE conversion.
     *
     * Maybe really UCS-2 without mb_string due to utf8_to_unicode limits
     */
    public static function utf16be_to_utf8(&$str)
    {
        $uni = unpack('n*',$str);
        return self::unicode_to_utf8($uni);
    }
}
interface Stemmer
{
    /**
     * Main function to get the STEM of a word
     *
     * @param string $word A valid UTF-8 word
     *
     * @return string
     *
     * @throws \Exception
     */
    public function stem($word);
}
abstract class Stem implements Stemmer
{
    protected static $vowels = array('a', 'e', 'i', 'o', 'u', 'y');
    /**
     * helper, contains stringified list of vowels
     * @var string
     */
    protected $plainVowels;
    /**
     * The word we are stemming
     * @var string
     */
    protected $word;
    /**
     * The original word, use to check if word has been modified
     * @var string
     */
    protected $originalWord;
    /**
     * RV value
     * @var string
     */
    protected $rv;
    /**
     * RV index (based on the beginning of the word)
     * @var integer
     */
    protected $rvIndex;
    /**
     * R1 value
     * @var integer
     */
    protected $r1;
    /**
     * R1 index (based on the beginning of the word)
     * @var int
     */
    protected $r1Index;
    /**
     * R2 value
     * @var integer
     */
    protected $r2;
    /**
     * R2 index (based on the beginning of the word)
     * @var int
     */
    protected $r2Index;
    protected function inRv($position)
    {
        return ($position >= $this->rvIndex);
    }
    protected function inR1($position)
    {
        return ($position >= $this->r1Index);
    }
    protected function inR2($position)
    {
        return ($position >= $this->r2Index);
    }
    protected function searchIfInRv($suffixes)
    {
        return $this->search($suffixes, $this->rvIndex);
    }
    protected function searchIfInR1($suffixes)
    {
        return $this->search($suffixes, $this->r1Index);
    }
    protected function searchIfInR2($suffixes)
    {
        return $this->search($suffixes, $this->r2Index);
    }
    protected function search($suffixes, $offset = 0)
    {
        $length = Utf8::strlen($this->word);
        if ($offset > $length) {
            return false;
        }
        foreach ($suffixes as $suffixe) {
            if ( (($position = Utf8::strrpos($this->word, $suffixe, $offset)) !== false) && ((Utf8::strlen($suffixe)+$position) == $length) ) {
                return $position;
            }
        }
        return false;
    }
    /**
     * R1 is the region after the first non-vowel following a vowel, or the end of the word if there is no such non-vowel.
     */
    protected function r1()
    {
        list($this->r1Index, $this->r1) = $this->rx($this->word);
    }
    /**
     * R2 is the region after the first non-vowel following a vowel in R1, or the end of the word if there is no such non-vowel.
     */
    protected function r2()
    {
        list($index, $value) = $this->rx($this->r1);
        $this->r2 = $value;
        $this->r2Index = $this->r1Index + $index;
    }
    /**
     * Common function for R1 and R2
     * Search the region after the first non-vowel following a vowel in $word, or the end of the word if there is no such non-vowel.
     * R1 : $in = $this->word
     * R2 : $in = R1
     */
    protected function rx($in)
    {
        $length = Utf8::strlen($in);
        // defaults
        $value = '';
        $index = $length;
        // we search all vowels
        $vowels = array();
        for ($i=0; $i<$length; $i++) {
            $letter = Utf8::substr($in, $i, 1);
            if (in_array($letter, static::$vowels)) {
                $vowels[] = $i;
            }
        }
        // search the non-vowel following a vowel
        foreach ($vowels as $position) {
            $after = $position + 1;
            $letter = Utf8::substr($in, $after, 1);
            if (! in_array($letter, static::$vowels)) {
                $index = $after + 1;
                $value = Utf8::substr($in, ($after+1));
                break;
            }
        }
        return array($index, $value);
    }
    /**
     * Used by spanish, italian, portuguese, etc (but not by french)
     *
     * If the second letter is a consonant, RV is the region after the next following vowel,
     * or if the first two letters are vowels, RV is the region after the next consonant,
     * and otherwise (consonant-vowel case) RV is the region after the third letter.
     * But RV is the end of the word if these positions cannot be found.
     */
    protected function rv()
    {
        $length = Utf8::strlen($this->word);
        $this->rv = '';
        $this->rvIndex = $length;
        if ($length < 3) {
            return true;
        }
        $first = Utf8::substr($this->word, 0, 1);
        $second = Utf8::substr($this->word, 1, 1);
        // If the second letter is a consonant, RV is the region after the next following vowel,
        if (!in_array($second, static::$vowels)) {
            for ($i=2; $i<$length; $i++) {
                $letter = Utf8::substr($this->word, $i, 1);
                if (in_array($letter, static::$vowels)) {
                    $this->rvIndex = $i + 1;
                    $this->rv = Utf8::substr($this->word, ($i+1));
                    return true;
                }
            }
        }
        // or if the first two letters are vowels, RV is the region after the next consonant,
        if ( (in_array($first, static::$vowels)) && (in_array($second, static::$vowels)) ) {
            for ($i=2; $i<$length; $i++) {
                $letter = Utf8::substr($this->word, $i, 1);
                if (! in_array($letter, static::$vowels)) {
                    $this->rvIndex = $i + 1;
                    $this->rv = Utf8::substr($this->word, ($i+1));
                    return true;
                }
            }
        }
        // and otherwise (consonant-vowel case) RV is the region after the third letter.
        if ( (! in_array($first, static::$vowels)) && (in_array($second, static::$vowels)) ) {
            $this->rv = Utf8::substr($this->word, 3);
            $this->rvIndex = 3;
            return true;
        }
    }
}
class English extends Stem
{
    /**
     * All english vowels
     */
    protected static $vowels = array('a', 'e', 'i', 'o', 'u', 'y');
    protected static $doubles = array('bb', 'dd', 'ff', 'gg', 'mm', 'nn', 'pp', 'rr', 'tt');
    protected static $liEnding = array('c', 'd', 'e', 'g', 'h', 'k', 'm', 'n', 'r', 't');
    /**
     * {@inheritdoc}
     */
    public function stem($word)
    {
        // we do ALL in UTF-8
        if (! Utf8::check($word)) {
            throw new \Exception('Word must be in UTF-8');
        }
        if (Utf8::strlen($word) < 3) {
            return $word;
        }
        $this->word = Utf8::strtolower($word);
        // exceptions
        if (null !== ($word = $this->exception1())) {
            return $word;
        }
        $this->plainVowels = implode('', self::$vowels);
        // Remove initial ', if present.
        $first = Utf8::substr($this->word, 0, 1);
        if ($first == "'") {
            $this->word = Utf8::substr($this->word, 1);
        }
        // Set initial y, or y after a vowel, to Y
        if ($first == 'y') {
            $this->word = preg_replace('#^y#u', 'Y', $this->word);
        }
        $this->word = preg_replace('#(['.$this->plainVowels.'])y#u', '$1Y', $this->word);
        $this->r1();
        $this->exceptionR1();
        $this->r2();
        $this->step0();
        $this->step1a();
        // exceptions 2
        if (null !== ($word = $this->exception2())) {
            return $word;
        }
        $this->step1b();
        $this->step1c();
        $this->step2();
        $this->step3();
        $this->step4();
        $this->step5();
        $this->finish();
        return $this->word;
    }
    /**
     * Step 0
     * Remove ', 's, 's'
     */
    private function step0()
    {
        if ( ($position = $this->search(array("'s'", "'s", "'"))) !== false) {
            $this->word = Utf8::substr($this->word, 0, $position);
        }
    }
    private function step1a()
    {
        // sses
        //      replace by ss
        if ( ($position = $this->search(array('sses'))) !== false) {
            $this->word = preg_replace('#(sses)$#u', 'ss', $this->word);
            return true;
        }
        // ied+   ies*
        //      replace by i if preceded by more than one letter, otherwise by ie (so ties -> tie, cries -> cri)
        if ( ($position = $this->search(array('ied', 'ies'))) !== false) {
            if ($position > 1) {
                $this->word = preg_replace('#(ied|ies)$#u', 'i', $this->word);
            } else {
                $this->word = preg_replace('#(ied|ies)$#u', 'ie', $this->word);
            }
            return true;
        }
        // us+   ss
        //  do nothing
        if ( ($position = $this->search(array('us', 'ss'))) !== false) {
            return true;
        }
        // s
        //      delete if the preceding word part contains a vowel not immediately before the s (so gas and this retain the s, gaps and kiwis lose it)
        if ( ($position = $this->search(array('s'))) !== false) {
            for ($i=0; $i<$position-1; $i++) {
                $letter = Utf8::substr($this->word, $i, 1);
                if (in_array($letter, self::$vowels)) {
                    $this->word = Utf8::substr($this->word, 0, $position);
                    return true;
                }
            }
            return true;
        }
        return false;
    }
    /**
     * Step 1b
     */
    private function step1b()
    {
        // eed   eedly+
        //      replace by ee if in R1
        if ( ($position = $this->search(array('eedly', 'eed'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(eedly|eed)$#u', 'ee', $this->word);
            }
            return true;
        }
        // ed   edly+   ing   ingly+
        //      delete if the preceding word part contains a vowel, and after the deletion:
        //      if the word ends at, bl or iz add e (so luxuriat -> luxuriate), or
        //      if the word ends with a double remove the last letter (so hopp -> hop), or
        //      if the word is short, add e (so hop -> hope)
        if ( ($position = $this->search(array('edly', 'ingly', 'ed', 'ing'))) !== false) {
            for ($i=0; $i<$position; $i++) {
                $letter = Utf8::substr($this->word, $i, 1);
                if (in_array($letter, self::$vowels)) {
                    $this->word = Utf8::substr($this->word, 0, $position);
                    if ($this->search(array('at', 'bl', 'iz')) !== false) {
                        $this->word .= 'e';
                    } elseif ( ($position2 = $this->search(self::$doubles)) !== false) {
                        $this->word = Utf8::substr($this->word, 0, ($position2+1));
                    } elseif ($this->isShort()) {
                        $this->word .= 'e';
                    }
                    return true;
                }
            }
            return true;
        }
        return false;
    }
    /**
     * Step 1c: *
     */
    private function step1c()
    {
        // replace suffix y or Y by i if preceded by a non-vowel
        // which is not the first letter of the word (so cry -> cri, by -> by, say -> say)
        $length = Utf8::strlen($this->word);
        if ($length < 3) {
            return true;
        }
        if ( ($position = $this->search(array('y', 'Y'))) !== false) {
            $before = $position - 1;
            $letter = Utf8::substr($this->word, $before, 1);
            if (! in_array($letter, self::$vowels)) {
                $this->word = preg_replace('#(y|Y)$#u', 'i', $this->word);
            }
            return true;
        }
        return false;
    }
    /**
     * Step 2
     *  Search for the longest among the following suffixes, and, if found and in R1, perform the action indicated.
     */
    private function step2()
    {
        // iveness   iviti:   replace by ive
        if ( ($position = $this->search(array('iveness', 'iviti'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(iveness|iviti)$#u', 'ive', $this->word);
            }
            return true;
        }
        // ousli   ousness:   replace by ous
        if ( ($position = $this->search(array('ousli', 'ousness'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(ousli|ousness)$#u', 'ous', $this->word);
            }
            return true;
        }
        // izer   ization:   replace by ize
        if ( ($position = $this->search(array('izer', 'ization'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(izer|ization)$#u', 'ize', $this->word);
            }
            return true;
        }
        // ational   ation   ator:   replace by ate
        if ( ($position = $this->search(array('ational', 'ation', 'ator'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(ational|ation|ator)$#u', 'ate', $this->word);
            }
            return true;
        }
        // biliti   bli+:   replace by ble
        if ( ($position = $this->search(array('biliti', 'bli'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(biliti|bli)$#u', 'ble', $this->word);
            }
            return true;
        }
        // lessli+:   replace by less
        if ( ($position = $this->search(array('lessli'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(lessli)$#u', 'less', $this->word);
            }
            return true;
        }
        // fulness:   replace by ful
        if ( ($position = $this->search(array('fulness', 'fulli'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(fulness|fulli)$#u', 'ful', $this->word);
            }
            return true;
        }
        // tional:   replace by tion
        if ( ($position = $this->search(array('tional'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(tional)$#u', 'tion', $this->word);
            }
            return true;
        }
        // alism   aliti   alli:   replace by al
        if ( ($position = $this->search(array('alism', 'aliti', 'alli'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(alism|aliti|alli)$#u', 'al', $this->word);
            }
            return true;
        }
        // enci:   replace by ence
        if ( ($position = $this->search(array('enci'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(enci)$#u', 'ence', $this->word);
            }
            return true;
        }
        // anci:   replace by ance
        if ( ($position = $this->search(array('anci'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(anci)$#u', 'ance', $this->word);
            }
            return true;
        }
        // abli:   replace by able
        if ( ($position = $this->search(array('abli'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(abli)$#u', 'able', $this->word);
            }
            return true;
        }
        // entli:   replace by ent
        if ( ($position = $this->search(array('entli'))) !== false) {
            if ($this->inR1($position)) {
                $this->word = preg_replace('#(entli)$#u', 'ent', $this->word);
            }
            return true;
        }
        // ogi+:   replace by og if preceded by l
        if ( ($position = $this->search(array('ogi'))) !== false) {
            if ($this->inR1($position)) {
                $before = $position - 1;
                $letter = Utf8::substr($this->word, $before, 1);
                if ($letter == 'l') {
                    $this->word = preg_replace('#(ogi)$#u', 'og', $this->word);
                }
            }
            return true;
        }
        // li+:   delete if preceded by a valid li-ending
        if ( ($position = $this->search(array('li'))) !== false) {
            if ($this->inR1($position)) {
                // a letter for you
                $letter = Utf8::substr($this->word, ($position-1), 1);
                if (in_array($letter, self::$liEnding)) {
                    $this->word = Utf8::substr($this->word, 0, $position);
                }
            }
            return true;
        }
        return false;
    }
    /**
     * Step 3:
     * Search for the longest among the following suffixes, and, if found and in R1, perform the action indicated.
     */
    public function step3()
    {
        // ational+:   replace by ate
        if ($this->searchIfInR1(array('ational')) !== false) {
            $this->word = preg_replace('#(ational)$#u', 'ate', $this->word);
            return true;
        }
        // tional+:   replace by tion
        if ($this->searchIfInR1(array('tional')) !== false) {
            $this->word = preg_replace('#(tional)$#u', 'tion', $this->word);
            return true;
        }
        // alize:   replace by al
        if ($this->searchIfInR1(array('alize')) !== false) {
            $this->word = preg_replace('#(alize)$#u', 'al', $this->word);
            return true;
        }
        // icate   iciti   ical:   replace by ic
        if ($this->searchIfInR1(array('icate', 'iciti', 'ical')) !== false) {
            $this->word = preg_replace('#(icate|iciti|ical)$#u', 'ic', $this->word);
            return true;
        }
        // ful   ness:   delete
        if ( ($position = $this->searchIfInR1(array('ful', 'ness'))) !== false) {
            $this->word = Utf8::substr($this->word, 0, $position);
            return true;
        }
        // ative*:   delete if in R2
        if ( (($position = $this->searchIfInR1(array('ative'))) !== false) && ($this->inR2($position)) )  {
            $this->word = Utf8::substr($this->word, 0, $position);
            return true;
        }
        return false;
    }
    /**
     * Step 4
     * Search for the longest among the following suffixes, and, if found and in R2, perform the action indicated.
     */
    public function step4()
    {
        //    ement  ance   ence  able ible   ant  ment   ent   ism   ate   iti   ous   ive   ize al  er   ic
        //      delete
        if ( ($position = $this->search(array(
            'ance', 'ence', 'ement', 'able', 'ible', 'ant', 'ment', 'ent', 'ism',
            'ate', 'iti', 'ous', 'ive', 'ize', 'al', 'er', 'ic'))) !== false) {
            if ($this->inR2($position)) {
                $this->word = Utf8::substr($this->word, 0, $position);
            }
            return true;
        }
        // ion
        //      delete if preceded by s or t
        if ( ($position = $this->searchIfInR2(array('ion'))) !== false) {
            $before = $position - 1;
            $letter = Utf8::substr($this->word, $before, 1);
            if ($letter == 's' || $letter == 't') {
                $this->word = Utf8::substr($this->word, 0, $position);
            }
            return true;
        }
        return false;
    }
    /**
     * Step 5: *
     * Search for the the following suffixes, and, if found, perform the action indicated.
     */
    public function step5()
    {
        // e
        //      delete if in R2, or in R1 and not preceded by a short syllable
        if ( ($position = $this->search(array('e'))) !== false) {
            if ($this->inR2($position)) {
                $this->word = Utf8::substr($this->word, 0, $position);
            } elseif ($this->inR1($position)) {
                if ( (! $this->searchShortSyllabe(-4, 3)) && (! $this->searchShortSyllabe(-3, 2)) ) {
                    $this->word = Utf8::substr($this->word, 0, $position);
                }
            }
            return true;
        }
        // l
        //      delete if in R2 and preceded by l
        if ( ($position = $this->searchIfInR2(array('l'))) !== false) {
            $before = $position - 1;
            $letter = Utf8::substr($this->word, $before, 1);
            if ($letter == 'l') {
                $this->word = Utf8::substr($this->word, 0, $position);
            }
            return true;
        }
        return false;
    }
    public function finish()
    {
        $this->word = Utf8::str_replace('Y', 'y', $this->word);
    }
    private function exceptionR1()
    {
        if (Utf8::strpos($this->word, 'gener') === 0) {
            $this->r1 = Utf8::substr($this->word, 5);
            $this->r1Index = 5;
        } elseif (Utf8::strpos($this->word, 'commun') === 0) {
            $this->r1 = Utf8::substr($this->word, 6);
            $this->r1Index = 6;
        } elseif (Utf8::strpos($this->word, 'arsen') === 0) {
            $this->r1 = Utf8::substr($this->word, 5);
            $this->r1Index = 5;
        }
    }
    /**
     *  1/ Stem certain special words as follows,
     *  2/ If one of the following is found, leave it invariant,
     */
    private function exception1()
    {
        $exceptions = array(
            'skis'   => 'ski',
            'skies'  => 'sky',
            'dying'  => 'die',
            'lying'  => 'lie',
            'tying'  => 'tie',
            'idly'   => 'idl',
            'gently' => 'gentl',
            'ugly'   => 'ugli',
            'early'  => 'earli',
            'only'   => 'onli',
            'singly' => 'singl',
            // invariants
            'sky'    => 'sky',
            'news'   => 'news',
            'howe'   => 'howe',
            'atlas'  => 'atlas',
            'cosmos' => 'cosmos',
            'bias'   => 'bias',
            'andes'  => 'andes'
        );
        if (isset($exceptions[$this->word])) {
            return $exceptions[$this->word];
        }
        return null;
    }
    /**
     * Following step 1a, leave the following invariant,
     */
    private function exception2()
    {
        $exceptions = array(
            'inning' => 'inning',
            'outing' => 'outing',
            'canning' => 'canning',
            'herring' => 'herring',
            'earring' => 'earring',
            'proceed' => 'proceed',
            'exceed'  => 'exceed',
            'succeed' => 'succeed'
        );
        if (isset($exceptions[$this->word])) {
            return $exceptions[$this->word];
        }
        return null;
    }
    /**
     *  A word is called short if it ends in a short syllable, and if R1 is null.
     *  Note : R1 not really null, but the word at this state must be smaller than r1 index
     *
     *  @return boolean
     */
    private function isShort()
    {
        $length = Utf8::strlen($this->word);
        return ( ($this->searchShortSyllabe(-3, 3) || $this->searchShortSyllabe(-2, 2)) && ($length == $this->r1Index) );
    }
    /**
     * Define a short syllable in a word as either (a) a vowel followed by a non-vowel other than w, x or Y and preceded by a non-vowel,
     *  or * (b) a vowel at the beginning of the word followed by a non-vowel.
     *
     *  So rap, trap, entrap end with a short syllable, and ow, on, at are classed as short syllables.
     *  But uproot, bestow, disturb do not end with a short syllable.
     */
    private function searchShortSyllabe($from, $nbLetters)
    {
        $length = Utf8::strlen($this->word);
        if ($from < 0) {
            $from = $length + $from;
        }
        if ($from < 0) {
            $from = 0;
        }
        // (a) is just for beginning of the word
        if ( ($nbLetters == 2) && ($from != 0) ) {
            return false;
        }
        $first = Utf8::substr($this->word, $from, 1);
        $second = Utf8::substr($this->word, ($from+1), 1);
        if ($nbLetters == 2) {
            if ( (in_array($first, self::$vowels)) && (!in_array($second, self::$vowels)) ) {
                return true;
            }
        }
        $third = Utf8::substr($this->word, ($from+2), 1);
        if ( (!in_array($first, self::$vowels)) && (in_array($second, self::$vowels))
            && (!in_array($third, array_merge(self::$vowels, array('x', 'Y', 'w'))))) {
                return true;
            }
        return false;
    }
}
?>