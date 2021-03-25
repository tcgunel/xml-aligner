<?php

namespace TCGunel\XmlAligner;

use ErrorException;
use Illuminate\Support\Collection;
use SimpleXMLElement;
use XMLReader;
use XMLWriter;

class XmlAligner
{
    public $data_structure;

    public $xml_stream;

    public $valid_xml_file_path;

    protected $new_xml_full_path;

    public $output_file_path;

    public function getDataStructure(): Collection
    {
        return collect($this->data_structure);
    }

    /**
     * @param array $data_structure
     *
     * @return XmlAligner
     */
    public function setDataStructure(array $data_structure): XmlAligner
    {
        $this->data_structure = $data_structure;

        return $this;
    }

    public function getXmlStream(): XMLReader
    {
        return $this->xml_stream;
    }

    /**
     * @param XMLReader $xml_stream
     *
     * @return XmlAligner
     */
    public function setXmlStream(XMLReader $xml_stream): XmlAligner
    {
        $this->xml_stream = $xml_stream;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNewXmlFullPath()
    {
        return $this->new_xml_full_path;
    }

    /**
     * @param mixed $new_xml_full_path
     *
     * @return XmlAligner
     */
    public function setNewXmlFullPath($new_xml_full_path): XmlAligner
    {
        $this->new_xml_full_path = $new_xml_full_path;

        return $this;
    }

    /**
     * @return string
     */
    public function getValidXmlFilePath(): string
    {
        return $this->valid_xml_file_path;
    }

    /**
     * @param mixed $valid_xml_file_path
     *
     * @return XmlAligner
     */
    public function setValidXmlFilePath(string $valid_xml_file_path): XmlAligner
    {
        $this->valid_xml_file_path = $valid_xml_file_path;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutputFilePath(): string
    {
        return $this->output_file_path;
    }

    /**
     * @param mixed $output_file_path
     */
    public function setOutputFilePath(string $output_file_path): XmlAligner
    {
        $this->output_file_path = $output_file_path;

        return $this;
    }

    public function readXmlFile(): XmlAligner
    {
        $xml = new XMLReader();

        $xml->open($this->getValidXmlFilePath());

        $this->setXmlStream($xml);

        return $this;
    }

    public function createNewXmlFile()
    {
        $xmlFile = $this->getOutputFilePath();

        $this->setNewXmlFullPath($xmlFile);

        $handle = fopen($xmlFile, "w");

        fclose($handle);
    }

    public function forEachPrimaryTag(): bool
    {
        $xml = $this->getXmlStream();

        $this->createNewXmlFile();

        foreach ($this->getDataStructure() as $primaryTag => $data) {

            while ($xml->read() && $xml->name !== $primaryTag) {
            }

            while ($xml->name === $primaryTag) {

                $xmlWriter = new XMLWriter();
                $xmlWriter->openMemory();

                try {
                    $element = new SimpleXMLElement($xml->readOuterXML());
                } catch (ErrorException $error_exception) {
                    return false;
                }

                $this->createXmlFromDataArray($element, [$primaryTag => $data], $xmlWriter);

                $this->appendTo($xmlWriter->flush(true), $this->getNewXmlFullPath());

                $xml->next($primaryTag);

                unset($element);
            }

        }

        $xml->close();

        $this->prependTo('<?xml version="1.0" encoding="UTF-8"?><items>', $this->getNewXmlFullPath());

        $this->appendTo('</items>', $this->getNewXmlFullPath());

        return true;
    }

    protected function appendTo($text, $file)
    {
        $handle = fopen($file, "a");
        fwrite($handle, $text);
        fclose($handle);
    }

    protected function prependTo($text, $file)
    {
        $src  = fopen($file, 'r+');
        $dest = fopen('php://temp', 'w');

        fwrite($dest, $text);

        stream_copy_to_stream($src, $dest);
        rewind($dest);
        rewind($src);
        stream_copy_to_stream($dest, $src);

        fclose($src);
        fclose($dest);
    }

    protected function createXmlFromDataArray(
        SimpleXMLElement $element,
        array $data,
        XMLWriter $xmlWriter,
        SimpleXMLElement $value_pointer = null
    ) {
        $node_key   = array_key_first($data);
        $node_value = $data[$node_key];

        if (is_array($node_value) && array_key_exists('xmlNode', $node_value)) {

            $xmlWriter->startElement($node_value["xmlNode"]);

        }

        if (isset($node_value['values']) && ! empty($node_value['values'])) {

            foreach ($node_value['values'] as $key => $value) {

                $value_pointer = $element->$node_key;

                self::createXmlFromDataArray($element, [$key => $value], $xmlWriter, $value_pointer);

            }

        } else {

            if (strpos($node_value, '[]') !== false) {

                if (isset($value_pointer->$node_key) && count($value_pointer->$node_key)){

                    $clean_node_name = str_replace('[]', '', $node_value);

                    foreach ($value_pointer->$node_key as $ea) {

                        $xmlWriter->startElement($clean_node_name);
                        $xmlWriter->writeCdata((string)$ea);
                        $xmlWriter->endElement();

                    }

                }

            } else {

                $value = $value_pointer ? (string)$value_pointer->$node_key : (string)$element->$node_key;

                if ( ! empty($value)) {

                    $xmlWriter->startElement($node_value);
                    $xmlWriter->writeCdata($value);
                    $xmlWriter->endElement();

                }

            }

        }

        if (is_array($node_value) && array_key_exists('xmlNode', $node_value)) {

            $xmlWriter->endElement();

        }
    }

    public function convert(): bool
    {
        return $this->readXmlFile()->forEachPrimaryTag();
    }

}
