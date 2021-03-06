package com.miraclelinux.historygluon;

import org.junit.Test;
import static org.junit.Assert.*;

public class UtilsTest
{
    @Test
    public void testToDoubleAsUnsigned() {
        double delta = 0.0f;
        assertEquals(0f, Utils.toDoubleAsUnsigned(0L), delta);
        assertEquals(255f, Utils.toDoubleAsUnsigned(0xffL), delta);
        assertEquals(1000000000d, Utils.toDoubleAsUnsigned(0x3b9aca00L), delta);
        assertEquals(2147483647d, Utils.toDoubleAsUnsigned(0x7fffffffL), delta);
        assertEquals(2147483648d, Utils.toDoubleAsUnsigned(0x80000000L), delta);
        assertEquals(1311768467463790320d, Utils.toDoubleAsUnsigned(0x123456789abcdef0L), delta);
        assertEquals(9223372036854775807d, Utils.toDoubleAsUnsigned(0x7fffffffffffffffL), delta);
        assertEquals(9223372036854775808d, Utils.toDoubleAsUnsigned(0x8000000000000000L), delta);
        assertEquals(18446744073709551615d, Utils.toDoubleAsUnsigned(0xffffffffffffffffL), delta);
    }

    @Test
    public void testCompareAsUnsignedInt() {
        assertEquals(0, Utils.compareAsUnsigned(0x00000000, 0x00000000));
        assertEquals(0, Utils.compareAsUnsigned(0x00000001, 0x00000001));
        assertEquals(0, Utils.compareAsUnsigned(0x12345678, 0x12345678));
        assertEquals(0, Utils.compareAsUnsigned(0x7fffffff, 0x7fffffff));
        assertEquals(0, Utils.compareAsUnsigned(0x80000000, 0x80000000));
        assertEquals(0, Utils.compareAsUnsigned(0xffffffff, 0xffffffff));

        assertTrue(Utils.compareAsUnsigned(0x00000000, 0x12345678) < 0);
        assertTrue(Utils.compareAsUnsigned(0x12345678, 0x87654321) < 0);
        assertTrue(Utils.compareAsUnsigned(0x00000000, 0x7fffffff) < 0);
        assertTrue(Utils.compareAsUnsigned(0x00000000, 0x80000000) < 0);
        assertTrue(Utils.compareAsUnsigned(0x00000000, 0xffffffff) < 0);
        assertTrue(Utils.compareAsUnsigned(0x7fffffff, 0x80000000) < 0);
        assertTrue(Utils.compareAsUnsigned(0x7fffffff, 0xffffffff) < 0);
        assertTrue(Utils.compareAsUnsigned(0x80000000, 0xffffffff) < 0);
        assertTrue(Utils.compareAsUnsigned(0xfffffffe, 0xffffffff) < 0);

        assertTrue(Utils.compareAsUnsigned(0x12345678, 0x00000000) > 0);
        assertTrue(Utils.compareAsUnsigned(0x87654321, 0x12345678) > 0);
        assertTrue(Utils.compareAsUnsigned(0x7fffffff, 0x00000000) > 0);
        assertTrue(Utils.compareAsUnsigned(0x80000000, 0x00000000) > 0);
        assertTrue(Utils.compareAsUnsigned(0x80000000, 0x7fffffff) > 0);
        assertTrue(Utils.compareAsUnsigned(0xffffffff, 0x7fffffff) > 0);
        assertTrue(Utils.compareAsUnsigned(0xffffffff, 0x80000000) > 0);
        assertTrue(Utils.compareAsUnsigned(0xffffffff, 0xfffffffe) > 0);
    }

    @Test
    public void testCompareAsUnsignedLong() {
        assertEquals(0, Utils.compareAsUnsigned(0x0000000000000000L, 0x0000000000000000L));
        assertEquals(0, Utils.compareAsUnsigned(0x0000000000000001L, 0x0000000000000001L));
        assertEquals(0, Utils.compareAsUnsigned(0x123456789abcdef0L, 0x123456789abcdef0L));
        assertEquals(0, Utils.compareAsUnsigned(0x7fffffffffffffffL, 0x7fffffffffffffffL));
        assertEquals(0, Utils.compareAsUnsigned(0x8000000000000000L, 0x8000000000000000L));
        assertEquals(0, Utils.compareAsUnsigned(0xffffffffffffffffL, 0xffffffffffffffffL));

        assertTrue(Utils.compareAsUnsigned(0x0000000000000000L, 0x123456789abcdef0L) < 0);
        assertTrue(Utils.compareAsUnsigned(0x123456789abcdef0L, 0x789abcdef0123456L) < 0);
        assertTrue(Utils.compareAsUnsigned(0x0000000000000000L, 0x7fffffffffffffffL) < 0);
        assertTrue(Utils.compareAsUnsigned(0x0000000000000000L, 0x8000000000000000L) < 0);
        assertTrue(Utils.compareAsUnsigned(0x0000000000000000L, 0xffffffffffffffffL) < 0);
        assertTrue(Utils.compareAsUnsigned(0x7fffffffffffffffL, 0x8000000000000000L) < 0);
        assertTrue(Utils.compareAsUnsigned(0x7fffffffffffffffL, 0xffffffffffffffffL) < 0);
        assertTrue(Utils.compareAsUnsigned(0x8000000000000000L, 0xffffffffffffffffL) < 0);
        assertTrue(Utils.compareAsUnsigned(0xfffffffffffffffeL, 0xffffffffffffffffL) < 0);

        assertTrue(Utils.compareAsUnsigned(0x123456789abcdef0L, 0x0000000000000000L) > 0);
        assertTrue(Utils.compareAsUnsigned(0x789abcdef0123456L, 0x0000000000000001L) > 0);
        assertTrue(Utils.compareAsUnsigned(0x7fffffffffffffffL, 0x0000000000000000L) > 0);
        assertTrue(Utils.compareAsUnsigned(0x8000000000000000L, 0x0000000000000000L) > 0);
        assertTrue(Utils.compareAsUnsigned(0x8000000000000000L, 0x7fffffffffffffffL) > 0);
        assertTrue(Utils.compareAsUnsigned(0xffffffffffffffffL, 0x7fffffffffffffffL) > 0);
        assertTrue(Utils.compareAsUnsigned(0xffffffffffffffffL, 0x8000000000000000L) > 0);
        assertTrue(Utils.compareAsUnsigned(0xffffffffffffffffL, 0xfffffffffffffffeL) > 0);
    }

    @Test
    public void testHexCharToIntNumeric() {
        assertEquals(0, Utils.hexCharToInt('0'));
        assertEquals(1, Utils.hexCharToInt('1'));
        assertEquals(2, Utils.hexCharToInt('2'));
        assertEquals(3, Utils.hexCharToInt('3'));
        assertEquals(4, Utils.hexCharToInt('4'));
        assertEquals(5, Utils.hexCharToInt('5'));
        assertEquals(6, Utils.hexCharToInt('6'));
        assertEquals(7, Utils.hexCharToInt('7'));
        assertEquals(8, Utils.hexCharToInt('8'));
        assertEquals(9, Utils.hexCharToInt('9'));
    }

    @Test
    public void testHexCharToIntHexAlphabetLower() {
        assertEquals(10, Utils.hexCharToInt('a'));
        assertEquals(11, Utils.hexCharToInt('b'));
        assertEquals(12, Utils.hexCharToInt('c'));
        assertEquals(13, Utils.hexCharToInt('d'));
        assertEquals(14, Utils.hexCharToInt('e'));
        assertEquals(15, Utils.hexCharToInt('f'));
    }

    @Test
    public void testHexCharToIntHexAlphabetUpper() {
        assertEquals(10, Utils.hexCharToInt('A'));
        assertEquals(11, Utils.hexCharToInt('B'));
        assertEquals(12, Utils.hexCharToInt('C'));
        assertEquals(13, Utils.hexCharToInt('D'));
        assertEquals(14, Utils.hexCharToInt('E'));
        assertEquals(15, Utils.hexCharToInt('F'));
    }

    @Test(expected = IllegalArgumentException.class)
    public void testHexCharToIntHexAlphabetInvalidUpperG() {
        Utils.hexCharToInt('G');
    }

    @Test(expected = IllegalArgumentException.class)
    public void testHexCharToIntHexAlphabetInvalidLowerG() {
        Utils.hexCharToInt('g');
    }

    @Test(expected = IllegalArgumentException.class)
    public void testHexCharToIntHexAlphabetInvalidSlash() {
        Utils.hexCharToInt('/');
    }

    @Test(expected = IllegalArgumentException.class)
    public void testHexCharToIntHexAlphabetInvalidColon() {
        Utils.hexCharToInt(':');
    }

    @Test(expected = IllegalArgumentException.class)
    public void testHexCharToIntHexAlphabetInvalidAt() {
        Utils.hexCharToInt('@');
    }

    @Test(expected = IllegalArgumentException.class)
    public void testHexCharToIntHexAlphabetInvalidBackQuartation() {
        Utils.hexCharToInt('`');
    }

    @Test
    public void testParseHexLong() {
        assertEquals(0x0001, Utils.parseHexLong("1"));
        assertEquals(0x0001, Utils.parseHexLong("01"));
        assertEquals(0x0001, Utils.parseHexLong("001"));
        assertEquals(0x0001, Utils.parseHexLong("0001"));
        assertEquals(0x0012, Utils.parseHexLong("12"));
        assertEquals(0x0123, Utils.parseHexLong("123"));
        assertEquals(0x1234, Utils.parseHexLong("1234"));
        assertEquals(0x00012345, Utils.parseHexLong("12345"));
        assertEquals(0x00123456, Utils.parseHexLong("123456"));
        assertEquals(0x01234567, Utils.parseHexLong("1234567"));
        assertEquals(0x12345678, Utils.parseHexLong("12345678"));
        assertEquals(0x000123456789L, Utils.parseHexLong("123456789"));
        assertEquals(0x00123456789aL, Utils.parseHexLong("123456789a"));
        assertEquals(0x0123456789abL, Utils.parseHexLong("123456789ab"));
        assertEquals(0x123456789abcL, Utils.parseHexLong("123456789abc"));
        assertEquals(0x000123456789abcdL, Utils.parseHexLong("123456789abcd"));
        assertEquals(0x00123456789abcdeL, Utils.parseHexLong("123456789abcde"));
        assertEquals(0x0123456789abcdefL, Utils.parseHexLong("123456789abcdef"));
        assertEquals(0x123456789abcdef0L, Utils.parseHexLong("123456789abcdef0"));
        assertEquals(0x7fffffffffffffffL, Utils.parseHexLong("7fffffffffffffff"));
        assertEquals(0x8000000000000000L, Utils.parseHexLong("8000000000000000"));
        assertEquals(0x8123456789abcdefL, Utils.parseHexLong("8123456789abcdef"));
        assertEquals(0x9abcdef012345678L, Utils.parseHexLong("9abcdef012345678"));
        assertEquals(0xabcdef0123456789L, Utils.parseHexLong("abcdef0123456789"));
        assertEquals(0xbcdef0123456789aL, Utils.parseHexLong("bcdef0123456789a"));
        assertEquals(0xcdef0123456789abL, Utils.parseHexLong("cdef0123456789ab"));
        assertEquals(0xdef0123456789abcL, Utils.parseHexLong("def0123456789abc"));
        assertEquals(0xef0123456789abcdL, Utils.parseHexLong("ef0123456789abcd"));
        assertEquals(0xf0123456789abcdeL, Utils.parseHexLong("f0123456789abcde"));
        assertEquals(0xffffffffffffffffL, Utils.parseHexLong("ffffffffffffffff"));
    }

    @Test
    public void testParseHexInt() {
        assertEquals(0x0001, Utils.parseHexLong("1"));
        assertEquals(0x0001, Utils.parseHexLong("01"));
        assertEquals(0x0001, Utils.parseHexLong("001"));
        assertEquals(0x0001, Utils.parseHexLong("0001"));
        assertEquals(0x0001, Utils.parseHexLong("00001"));
        assertEquals(0x0012, Utils.parseHexLong("12"));
        assertEquals(0x0123, Utils.parseHexLong("123"));
        assertEquals(0x1234, Utils.parseHexLong("1234"));
        assertEquals(0x00012345, Utils.parseHexLong("12345"));
        assertEquals(0x00123456, Utils.parseHexLong("123456"));
        assertEquals(0x01234567, Utils.parseHexLong("1234567"));
        assertEquals(0x12345678, Utils.parseHexLong("12345678"));
        assertEquals(0x7fffffffL, Utils.parseHexLong("7fffffff"));
        assertEquals(0x80000000L, Utils.parseHexLong("80000000"));
        assertEquals(0x81234567L, Utils.parseHexLong("81234567"));
        assertEquals(0x9abcdef0L, Utils.parseHexLong("9abcdef0"));
        assertEquals(0xabcdef01L, Utils.parseHexLong("abcdef01"));
        assertEquals(0xbcdef012L, Utils.parseHexLong("bcdef012"));
        assertEquals(0xcdef0123L, Utils.parseHexLong("cdef0123"));
        assertEquals(0xdef01234L, Utils.parseHexLong("def01234"));
        assertEquals(0xef012345L, Utils.parseHexLong("ef012345"));
        assertEquals(0xf0123456L, Utils.parseHexLong("f0123456"));
        assertEquals(0xffffffffL, Utils.parseHexLong("ffffffff"));
    }

    /* -----------------------------------------------------------------------
     * Private methods
     * -------------------------------------------------------------------- */
}
