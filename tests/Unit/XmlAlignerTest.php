<?php

namespace TCGunel\XmlAligner\Tests\Unit;

use Illuminate\Support\Facades\Storage;
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
                    "kategori"    => "categoryTree",
                    "urunadi"     => "name",
                    "urunid"      => "code",
                    "detay"       => "description",
                    "resimler"    => [
                        "xmlNode" => "pictures[]",
                        "values"  => [
                            "resim" => "picture",
                        ],
                    ],
                    "stok"        => "stock",
                    "fiyat"       => "price",
                    "para_birimi" => "currency",
                    "kdv"         => "tax",
                    "varyantlar"  => [
                        "xmlNode" => "variants[]",
                        "values"  => [
                            "varyant" => [
                                "xmlNode" => "variant",
                                "values"  => [
                                    "spec[name]" => "spek[test]",
                                    "tip"        => "name",
                                    "deger"      => "value",
                                    "stok"       => "stock",
                                    "fiyat"      => "price",
                                    "resimler"   => [
                                        "xmlNode" => "pictures[]",
                                        "values"  => [
                                            "resim" => "picture",
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $instance = new XmlAligner();

        $xml_file = __DIR__ . '/../../storage/app/public/tests/source.xml';

        if (!Storage::disk("local")->exists("public/tests/")) {

            Storage::disk("local")->makeDirectory("public/tests");

        }

        $output_path = Storage::disk("local")->path("public/tests/output.xml");

        $result = $instance
            ->setDataStructure($format)
            ->setValidXmlFilePath($xml_file)
            ->setOutputFilePath($output_path)
            ->convert();

        $this->assertXmlFileEqualsXmlFile(
            __DIR__ . '/../../storage/app/public/tests/target.xml',
            $output_path
        );
        $this->assertTrue($result);
    }
}
