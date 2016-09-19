<?php

namespace GoogleContacts;

class GroupEntry
{

	private $title;

	function __construct($name)
	{
		$this->title = $name;
	}

	function get_name()
	{
		return $this->title;
	}

	function set_name($name)
	{
		$this->title = $name;
	}

	function getXml()
	{
		$xml = new \XMLWriter();
		$xml->openMemory();

		$xml->startElement('entry');
		$xml->writeAttribute('xmlns', 'http://www.w3.org/2005/Atom');
		$xml->writeAttribute('xmlns:gd', 'http://schemas.google.com/g/2005');
		$xml->writeAttribute('gd:etag', '&quot;Rno4ezVSLyp7ImA9WxdTEUgNRQU.&quot;');

		$xml->startElement('category');
		$xml->writeAttribute('scheme', 'http://schemas.google.com/g/2005#kind');
		$xml->writeAttribute('term', 'http://schemas.google.com/g/2005#group');
		$xml->endElement();

		$xml->writeElement('title', $this->get_name());

		$xml->endElement();

		return $xml->outputMemory();
	}

}

