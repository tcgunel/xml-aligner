<?php

namespace TCGunel\XmlAligner\Tests\Unit;

use TCGunel\XmlAligner\XmlAligner;
use TCGunel\XmlAligner\Tests\TestCase;

class XmlAlignerTest extends TestCase
{
    public $dummy_data_structure = [
        'a',
        'b',
        'c',
    ];

    public function setUp(): void
    {
        parent::setUp();

    }

    public function test_can_get_data_structure()
    {
        $instance = new XmlAligner();

        $instance->setDataStructure($this->dummy_data_structure);

        $this->assertEquals(collect($this->dummy_data_structure), $instance->getDataStructure());
    }

    public function test_can_set_output_xml_tags()
    {
        $instance = new XmlAligner();

        $instance->setDataStructure($this->dummy_data_structure);

        $this->assertEquals($this->dummy_data_structure, $instance->data_structure);
    }

    public function test_can_convert_xml()
    {
        $format = [
            "urun" => [
                "xmlNode" => "item",
                "values"  => [
                    "kategori" => "categoryTree",
                    "urunadi"  => "name",
                    "urunid"   => "code",
                    "detay"    => "description",
                    "resimler" => [
                        "xmlNode" => "pictures",
                        "values"  => [
                            "resim" => "picture[]",
                        ],
                    ],
                ],
            ],
        ];

        $instance = new XmlAligner();

        $xml_file    = __DIR__ . '/../../storage/public/test.xml';
        $output_path = __DIR__ . '/../../storage/public/outputs/';

        $result = $instance
            ->setDataStructure($format)
            ->setValidXmlFilePath($xml_file)
            ->setOutputPath($output_path)
            ->convert();

        $this->assertFileExists($instance->getOutputPath() . $instance->getOutputFileName());
        $this->assertTrue($result);
    }
}
