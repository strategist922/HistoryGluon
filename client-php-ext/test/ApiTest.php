<?php

define("HGL_SUCCESS",             0);
define("HGLERR_TOO_LONG_DB_NAME", -11);
define("HGLERR_INVALID_DB_NAME",  -12);
define("HGLPHPERR__FAILED_ADD_TO_HASH_TABLE",  -1000010);

define("HISTORY_GLUON_DELETE_TYPE_EQUAL",            0);
define("HISTORY_GLUON_DELETE_TYPE_EQUAL_OR_LESS",    1);
define("HISTORY_GLUON_DELETE_TYPE_LESS",             2);
define("HISTORY_GLUON_DELETE_TYPE_EQUAL_OR_GREATER", 3);
define("HISTORY_GLUON_DELETE_TYPE_GREATER",          4);

define("HISTORY_GLUON_DATA_KEY_ID",     "id");
define("HISTORY_GLUON_DATA_KEY_SEC",    "sec");
define("HISTORY_GLUON_DATA_KEY_NS",     "ns");
define("HISTORY_GLUON_DATA_KEY_TYPE",   "type");
define("HISTORY_GLUON_DATA_KEY_VALUE",  "value");
define("HISTORY_GLUON_DATA_KEY_LENGTH", "length");

define("HISTORY_GLUON_DATA_TYPE_FLOAT",  0);
define("HISTORY_GLUON_DATA_TYPE_STRING", 1);
define("HISTORY_GLUON_DATA_TYPE_UINT",   2);
define("HISTORY_GLUON_DATA_TYPE_BLOB",   3);

define("HISTORY_GLUON_SORT_ASCENDING",  0);
define("HISTORY_GLUON_SORT_DESCENDING", 1);
define("HISTORY_GLUON_SORT_NOT_SORTED", 2);

define("HISTORY_GLUON_TIME_START_SEC",  0x00000000);
define("HISTORY_GLUON_TIME_START_NS",   0x00000000);
define("HISTORY_GLUON_TIME_END_SEC",    0x99999999);
define("HISTORY_GLUON_TIME_END_NS",     0x99999999);

define("HISTORY_GLUON_MAX_DATABASE_NAME_LENGTH", 1024);

define("HISTORY_GLUON_NUM_ENTRIES_UNLIMITED",  0);

define("HISTORY_GLUON_DATA_KEY_ARRAY_SORT_ORDER", "sort_order");
define("HISTORY_GLUON_DATA_KEY_ARRAY_ARRAY",      "array");


define("TEST_ID_UINT",   0x358);
define("TEST_ID_FLOAT",  0x12345678);
define("TEST_ID_STRING", 0x87654321);
define("TEST_ID_BLOB",   0x102030405060);

define("TEST_NUM_SAMPLES", 5);

class ApiTest extends PHPUnit_Framework_TestCase
{
    /* ------------------------------------------------------------------------
     * Private Members
     * --------------------------------------------------------------------- */
    private $g_ctx = null;
    private $g_ctx_array = array();

    /* ------------------------------------------------------------------------
     * Test Methods
     * --------------------------------------------------------------------- */
    protected function setUp() {
        if (!extension_loaded('History Gluon PHP Extension'))
            dl("history_gluon.so");
    }

    protected function tearDown() {
        if ($this->g_ctx != null) {
            history_gluon_free_context($this->g_ctx);
            $this->g_ctx = null;
        }

        if ($this->g_ctx_array != null) {
            foreach($this->g_ctx_array as $key => $ctx)
                history_gluon_free_context($ctx);
            $this->g_ctx_array = array();
        }
    }

    public function testCreateContext() {
        $ctx = null;
        $ret = history_gluon_create_context("test", null, 0, $ctx);
        $this->assertEquals(HGL_SUCCESS, $ret);
        $this->assertGreaterThan(0, $ctx);
    }

    public function testFreeContext() {
        $this->assertGloblCreateContext();
        history_gluon_free_context($this->g_ctx);
        $this->g_ctx = null;
    }

    public function testCreateContextLocalhost() {
        $ret = history_gluon_create_context("test", "localhost", 0, $this->g_ctx);
        $this->assertEquals(HGL_SUCCESS, $ret);
        $this->assertGreaterThan(0, $this->g_ctx);
    }

    public function testCreateContext127_0_0_1() {
        $ret = history_gluon_create_context("test", "127.0.0.1", 0, $this->g_ctx);
        $this->assertEquals(HGL_SUCCESS, $ret);
        $this->assertGreaterThan(0, $this->g_ctx);
    }

    public function testCreateContextPort30010() {
        $ret = history_gluon_create_context("test", null, 30010, $this->g_ctx);
        $this->assertEquals(HGL_SUCCESS, $ret);
        $this->assertGreaterThan(0, $this->g_ctx);
    }

    public function testCreateContextValidDBName() {
        $dbname = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz012345689.-_@/";
        $ret = history_gluon_create_context($dbname, null, 0, $this->g_ctx);
        $this->assertEquals(HGL_SUCCESS, $ret);
        $this->assertGreaterThan(0, $this->g_ctx);
    }

    public function testCreateContextInvalidDBName() {
        $dbname = "name!";
        $ret = history_gluon_create_context($dbname, null, 0, $this->g_ctx);
        $this->assertEquals(HGLERR_INVALID_DB_NAME, $ret);
    }

    public function testCreateContextMaxDBName() {
        $len = HISTORY_GLUON_MAX_DATABASE_NAME_LENGTH;
        $this->assertCreateContextLongName($len, HGL_SUCCESS);
    }

    public function testCreateContextOverMaxDBName() {
        $len = HISTORY_GLUON_MAX_DATABASE_NAME_LENGTH + 1;
        $this->assertCreateContextLongName($len, HGLERR_TOO_LONG_DB_NAME);
    }

    public function testCreateContext1024Times() {
        $this->assertCreateContextManyTime(1024, HGL_SUCCESS);
    }

    public function testAddUint() {
        $id   = 0x12345678;
        $sec  = 10203040;
        $ns   = 200111500;
        $data = 0x87654321;
        $this->assertGloblCreateContext();
        $this->assertDeleteAllDataWithId($id);
        $ret = history_gluon_add_uint($this->g_ctx, $id, $sec, $ns, $data);
        $this->assertEquals(HGL_SUCCESS, $ret);
    }

    public function testAddFloat() {
        $id   = 0x20406080;
        $sec  = 222333444;
        $ns   = 100999100;
        $data = 0.21;
        $this->assertGloblCreateContext();
        $this->assertDeleteAllDataWithId($id);
        $ret = history_gluon_add_float($this->g_ctx, $id, $sec, $ns, $data);
        $this->assertEquals(HGL_SUCCESS, $ret);
    }

    public function testAddString() {
        $id   = 0x20406080;
        $sec  = 222333444;
        $ns   = 100999100;
        $data = "STRING Test !%?@";
        $this->assertGloblCreateContext();
        $this->assertDeleteAllDataWithId($id);
        $ret = history_gluon_add_string($this->g_ctx, $id, $sec, $ns, $data);
        $this->assertEquals(HGL_SUCCESS, $ret);
    }

    public function testAddBlob() {
        $id   = 0x876543210fb;
        $sec  = 234567890;
        $ns   =    999100;
        $data = pack("VVVV", 0x12345678, 0xffffaaaa, 0x2468ace0, 0x13579bdf);
        $this->assertGloblCreateContext();
        $this->assertDeleteAllDataWithId($id);
        $ret = history_gluon_add_blob($this->g_ctx, $id, $sec, $ns, $data);
        $this->assertEquals(HGL_SUCCESS, $ret);
    }

    public function testRangeQueryUintAsc() {
        $this->assertRangeQueryI2N2(TEST_ID_UINT, HISTORY_GLUON_SORT_ASCENDING);
    }

    public function testRangeQueryUintDsc() {
        $this->assertRangeQueryI2N2(TEST_ID_UINT, HISTORY_GLUON_SORT_DESCENDING);
    }

    public function testRangeQueryFloatAsc() {
        $this->assertRangeQueryI2N2(TEST_ID_FLOAT, HISTORY_GLUON_SORT_ASCENDING);
    }

    public function testRangeQueryFloatDsc() {
        $this->assertRangeQueryI2N2(TEST_ID_FLOAT, HISTORY_GLUON_SORT_DESCENDING);
    }

    public function testRangeQueryStringAsc() {
        $this->assertRangeQueryI2N2(TEST_ID_STRING, HISTORY_GLUON_SORT_ASCENDING);
    }

    public function testRangeQueryStringDsc() {
        $this->assertRangeQueryI2N2(TEST_ID_STRING, HISTORY_GLUON_SORT_DESCENDING);
    }

    public function testRangeQueryBlobAsc() {
        $this->assertRangeQueryI2N2(TEST_ID_BLOB, HISTORY_GLUON_SORT_ASCENDING);
    }

    public function testRangeQueryBLobDsc() {
        $this->assertRangeQueryI2N2(TEST_ID_BLOB, HISTORY_GLUON_SORT_DESCENDING);
    }

    /* -----------------------------------------------------------------------
     * Private Methods
     * --------------------------------------------------------------------- */
    private function assertGloblCreateContext() {
        $ret = history_gluon_create_context("test", null, 0, $this->g_ctx);
        $this->assertEquals(HGL_SUCCESS, $ret);
        $this->assertGreaterThan(0, $this->g_ctx);
    }

    private function assertDeleteAllDataWithId($id) {
        $numDeleted = 0;
        $ret = history_gluon_delete($this->g_ctx, $id, 0, 0,
                                    HISTORY_GLUON_DELETE_TYPE_EQUAL_OR_GREATER,
                                    $numDeleted);
        $this->assertEquals(HGL_SUCCESS, $ret);
    }

    private function assertCreateContextDeleteAddSamples($id) {
        $this->assertGloblCreateContext();
        $this->assertDeleteAllDataWithId($id);
        $getSamplesFuncName = $this->getSamplesAddFuncName($id);
        $addFuncName = $this->getAddFuncName($id);
        $this->assertAddSamples($getSamplesFuncName, $addFuncName);
    }

    private function getSamplesAddFuncName($id) {
        $name = null;
        if ($id == TEST_ID_UINT)
            $name = "getSamplesUint";
        else if ($id == TEST_ID_FLOAT)
            $name = "getSamplesFloat";
        else if ($id == TEST_ID_STRING)
            $name = "getSamplesString";
        else if ($id == TEST_ID_BLOB)
            $name = "getSamplesBlob";
        else
            $this->ssertFalse(TRUE);
        return $name;
    }

    private function getSamples($id) {
        $addFuncName = $this->getSamplesAddFuncName($id);
        return call_user_func(array($this, $addFuncName));
    }

    private function getAddFuncName($id) {
        $name = null;
        if ($id == TEST_ID_UINT)
            $name = "history_gluon_add_uint";
        else if ($id == TEST_ID_FLOAT)
            $name = "history_gluon_add_float";
        else if ($id == TEST_ID_STRING)
            $name = "history_gluon_add_string";
        else if ($id == TEST_ID_BLOB)
            $name = "history_gluon_add_blob";
        else
            $this->ssertFalse(TRUE);
        return $name;
    }

    private function assertAddSamples($getSamplesFunc, $addFuncName) {
        $samples = call_user_func(array($this, $getSamplesFunc));
        $numSamples = count($samples);
        for ($i = 0; $i < $numSamples; $i++) {
            $id   = $samples[$i][HISTORY_GLUON_DATA_KEY_ID];
            $sec  = $samples[$i][HISTORY_GLUON_DATA_KEY_SEC];
            $ns   = $samples[$i][HISTORY_GLUON_DATA_KEY_NS];
            $data = $samples[$i][HISTORY_GLUON_DATA_KEY_VALUE];
            $ret = call_user_func($addFuncName, $this->g_ctx, $id, $sec, $ns, $data);
            $this->assertEquals(HGL_SUCCESS, $ret);
        }
    }

    private function getSamplesUint() {
        $dataType = HISTORY_GLUON_DATA_TYPE_UINT;
        $samples[0] = $this->createSample(TEST_ID_UINT, 0x12345678, 100200300,
                                          $dataType, 0x43214321);
        $samples[1] = $this->createSample(TEST_ID_UINT, 0x20000000, 000000000,
                                          $dataType, 0x22223333);
        $samples[2] = $this->createSample(TEST_ID_UINT, 0x20000000, 000000003,
                                          $dataType, 0x9a9a8b8b);
        $samples[3] = $this->createSample(TEST_ID_UINT, 0x25010101, 200345003,
                                          $dataType, 0x9a9a8b8b00);
        $samples[4] = $this->createSample(TEST_ID_UINT, 0x353a0000, 879200003,
                                          $dataType, 0x876543210fedcba);
        return $samples;
    }

    private function getSamplesFloat() {
        $dataType = HISTORY_GLUON_DATA_TYPE_FLOAT;
        $samples[0] = $this->createSample(TEST_ID_FLOAT, 0x12345678, 100200300,
                                          $dataType, 0.25);
        $samples[1] = $this->createSample(TEST_ID_FLOAT, 0x20000000, 000000000,
                                          $dataType, -1.55e-8);
        $samples[2] = $this->createSample(TEST_ID_FLOAT, 0x20000000, 000000003,
                                          $dataType, 8e10);
        $samples[3] = $this->createSample(TEST_ID_FLOAT, 0x25010101, 200345003,
                                          $dataType, 222.555);
        $samples[4] = $this->createSample(TEST_ID_FLOAT, 0x353a0000, 879200003,
                                          $dataType, 8922.321234412343);
        return $samples;
    }

    private function getSamplesString() {
        $dataType = HISTORY_GLUON_DATA_TYPE_STRING;
        $samples[0] = $this->createSample(TEST_ID_STRING, 0x12345678, 100200300,
                                          $dataType, "Dog");
        $samples[1] = $this->createSample(TEST_ID_STRING, 0x20000000, 000000000,
                                          $dataType, "Care killed the cat.");
        $samples[2] = $this->createSample(TEST_ID_STRING, 0x20000000, 000000003,
                                          $dataType, "Clothes make the man.");
        $samples[3] = $this->createSample(TEST_ID_STRING, 0x25010101, 200345003,
                                          $dataType, "Roma was not build in a day.");
        $samples[4] = $this->createSample(TEST_ID_STRING, 0x353a0000, 879200003,
                                          $dataType, "Walls have ears.");
        return $samples;
    }

    private function getSamplesBlob() {
        $dataType = HISTORY_GLUON_DATA_TYPE_BLOB;
        $samples[0] = $this->createSample(TEST_ID_BLOB, 0x12005678, 100200300,
                                          $dataType,
                                          pack("CCCCCCCC", 0x43, 0x21, 0x43, 0x21,
                                                           0x00, 0x00, 0xff, 0xff));
        $samples[1] = $this->createSample(TEST_ID_BLOB, 0x20000000, 000000000,
                                          $dataType,
                                          pack("C", 0xab));
        $samples[2] = $this->createSample(TEST_ID_BLOB, 0x20000000, 000000003,
                                          $dataType,
                                          pack("CCCC", 0xa5, 0xb0, 0xc2, 0x2));
        $samples[3] = $this->createSample(TEST_ID_BLOB, 0x25010101, 200345003,
                                          $dataType,
                                          pack("CCCCCCCCCCCCCCCCCCCCCCCC",
                                               0x11, 0xf8, 0xb3, 0x41, 0x00, 0x34,
                                               0x27, 0x48, 0x55, 0x66, 0x89, 0x01,
                                               0x43, 0x24, 0xda, 0xd8, 0x32, 0x48,
                                               0x00, 0x05, 0x32, 0x48, 0x3f, 0xfe));
        $samples[4] = $this->createSample(TEST_ID_BLOB, 0x353a0000, 879200003,
                                          $dataType,
                                          pack("CCCCCC",
                                               0x43, 0x21, 0x43, 0x21, 0x00, 0xff));
        return $samples;
    }


    private function createSample($id, $sec, $ns, $dataType, $data) {
        $sample[HISTORY_GLUON_DATA_KEY_ID]   = $id;
        $sample[HISTORY_GLUON_DATA_KEY_SEC]  = $sec;
        $sample[HISTORY_GLUON_DATA_KEY_NS]   = $ns;
        $sample[HISTORY_GLUON_DATA_KEY_TYPE] = $dataType;
        $sample[HISTORY_GLUON_DATA_KEY_VALUE] = $data;
        if ($id == TEST_ID_STRING || $id == TEST_ID_BLOB)
            $sample[HISTORY_GLUON_DATA_KEY_LENGTH] = strlen($data);
        return $sample;
    }

    private function getSampleTime($id, $idx) {
        $samples = $this->getSamples($id);
        $sample = $samples[$idx];
        $sec = $sample[HISTORY_GLUON_DATA_KEY_SEC];
        $ns  = $sample[HISTORY_GLUON_DATA_KEY_NS];
        return array($sec, $ns);
    }

    private function addTime($time0, $time1) {
        $sec0 = $time0[0];
        $ns0  = $time0[1];
        $sec1 = $time1[0];
        $ns1  = $time1[1];
        $sumSec = $sec0 + $sec1;
        $sumNs = $sec0 + $sec1;

        $sumSec += $sumNs / 1000000000;
        $sumNs = $sumNs % 1000000000;
        return array($sumSec, $sumNs);
    }

    private function isTypeString($data)
    {
        if ($data[HISTORY_GLUON_DATA_KEY_TYPE] == HISTORY_GLUON_DATA_TYPE_STRING)
            return True;
        return False;
    }

    private function isTypeBlob($data)
    {
        if ($data[HISTORY_GLUON_DATA_KEY_TYPE] == HISTORY_GLUON_DATA_TYPE_BLOB)
            return True;
        return False;
    }

    private function assertCreateContextLongName($length, $expected) {
        $i = 0;
        $dbname = "";
        for ($i = 0; $i < $length; $i++)
            $dbname .= "A";
        $ret = history_gluon_create_context($dbname, null, 0, $this->g_ctx);
        $this->assertEquals($expected, $ret);
    }

    private function assertCreateContextManyTime($times, $expected) {
        $ctx = null;
        for ($i = 0; $i < $times; $i++) {
            $ret = history_gluon_create_context("test", null, 0, $ctx);
            $this->assertEquals($expected, $ret);
            array_push($this->g_ctx_array, $ctx);
        }
    }

    private function assertEqualGluonData($expected, $actual) {
            $this->assertEquals($expected[HISTORY_GLUON_DATA_KEY_ID],
                                $actual[HISTORY_GLUON_DATA_KEY_ID]);

            $this->assertEquals($expected[HISTORY_GLUON_DATA_KEY_SEC],
                                $actual[HISTORY_GLUON_DATA_KEY_SEC]);

            $this->assertEquals($expected[HISTORY_GLUON_DATA_KEY_NS],
                                $actual[HISTORY_GLUON_DATA_KEY_NS]);

            $this->assertEquals($expected[HISTORY_GLUON_DATA_KEY_TYPE],
                                $actual[HISTORY_GLUON_DATA_KEY_TYPE]);

            $this->assertEquals($expected[HISTORY_GLUON_DATA_KEY_VALUE],
                                $actual[HISTORY_GLUON_DATA_KEY_VALUE]);

            if ($this->isTypeString($expected) || $this->isTypeBlob($expected)) {
                $this->assertEquals($expected[HISTORY_GLUON_DATA_KEY_LENGTH],
                                    $actual[HISTORY_GLUON_DATA_KEY_LENGTH]);
            }
    }

    private function assertRangeQuery($id, $idx0, $num, $ts1plug1ns,
                                      $sortRequest,
                                      $expectedRetIdx0, $expectedNumRet) {
        $this->assertCreateContextDeleteAddSamples($id);
        $numMaxEntries = HISTORY_GLUON_NUM_ENTRIES_UNLIMITED;
        $array = null;
        $idx1 = $idx0 + $num;
        $time0 = $this->getSampleTime($id, $idx0);
        $time1 = $this->getSampleTime($id, $idx1);
        if ($ts1plug1ns == True) {
            $timeInc = array(0, 1);
            $time1 = $this->addTime($time1, $timeInc);
        }
        $sec0 = $time0[0];
        $ns0  = $time0[1];
        $sec1 = $time1[0];
        $ns1  = $time1[1];
        $ret = history_gluon_range_query($this->g_ctx, $id,
                                         $sec0, $ns0, $sec1, $ns1,
                                         $sortRequest, $numMaxEntries,
                                         $array);
        $this->assertEquals(HGL_SUCCESS, $ret);

        $arrArr = $array[HISTORY_GLUON_DATA_KEY_ARRAY_ARRAY];
        $numRetData = count($arrArr);
        $this->assertEquals($expectedNumRet, $numRetData);

        $samples = $this->getSamples($id);
        for ($i = 0; $i < $numRetData; $i++) {
            $expected_idx = $expectedRetIdx0 + $i;
            if ($sortRequest == HISTORY_GLUON_SORT_DESCENDING)
                $expected_idx = $expectedRetIdx0 + $expectedNumRet - $i - 1;
            $this->assertEqualGluonData($samples[$expected_idx], $arrArr[$i]);
        }
    }

    private function assertRangeQueryI2N2($id, $sortRequest) {
        $idx0 = 2;
        $num = 2;
        $ts1plug1ns = False;
        $expectedRetIdx0 = $idx0;
        $expectedNumRet = $num;
        $this->assertRangeQuery($id, $idx0, $num, $ts1plug1ns,
                                $sortRequest,
                                $expectedRetIdx0, $expectedNumRet);
    }
}
