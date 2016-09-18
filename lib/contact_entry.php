<?php

namespace GoogleContacts;

class ContactEntry {
    
    private $first_name;
    private $last_name;
    private $email;
    private $phone_number;
    private $group_id;     
  
    function __construct($first_name, $last_name, $email, $phone_number, $group_id) {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone_number = $phone_number;
        $this->group_id = $group_id;
    }
   
    function getXml() {
        $xml = new \XMLWriter();
        $xml->openMemory();

        $xml->startElement('atom:entry'); 
        $xml->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');   
        $xml->writeAttribute('xmlns:gd', 'http://schemas.google.com/g/2005');
        
        $xml->startElementNS('atom', 'category', null);     
        $xml->writeAttribute('scheme', 'http://schemas.google.com/g/2005#kind');
        $xml->writeAttribute('term', 'http://schemas.google.com/contact/2008#contact');
        $xml->endElement();
        
        $xml->startElementNS('gd', 'name', null);
        $xml->writeElementNS ('gd','givenName', null, $this->first_name); 
        $xml->writeElementNS ('gd','familyName', null, $this->last_name); 
        $xml->endElement();
        
        $xml->startElementNS('gd', 'email', null);
        $xml->writeAttribute('label', 'Personal');
        $xml->writeAttribute('address', $this->email);
        $xml->endElement();
        
        $xml->startElementNS('gd', 'phoneNumber', null);
        $xml->writeAttribute('label', 'Personal');
        $xml->text($this->phone_number);
        $xml->endElement();
        
        $xml->startElementNS('gContact', 'groupMembershipInfo', null);
        $xml->writeAttribute('href', $this->group_id);
        $xml->endElement();
        
        $xml->endElement();
        return $xml->outputMemory();
    }
}

