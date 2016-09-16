<?php

namespace GoogleContacts;

class ContactEntry {
    
    private $first_name;
    private $last_name;
    private $email;
    private $phone_number;
    private $group; 
    
    function __construct(Contact $contact) {
        $this->first_name = $contact->get_first_name();
        $this->last_name = $contact->get_first_name();
        $this->email = $contact->get_email();
        $this->phone_number = $contact->get_phone_number();
        $this->group = new GroupEntry($contact->get_contacts_group_title());
    }
    
    function get_first_name() {
        return $this->first_name;
    }

    function get_last_name() {
        return $this->last_name;
    }

    function get_email() {
        return $this->email;
    }

    function get_phone_number() {
        return $this->phone_number;
    }

    function get_group() {
        return $this->group;
    }
    
    function get_xml() {
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
        $xml->writeElementNS ('gd','givenName', null, $this->get_first_name()); 
        $xml->writeElementNS ('gd','familyName', null, $this->get_last_name()); 
        $xml->writeElementNS ('gd','fullName', null, $this->get_first_name().' '.$this->get_last_name()); 
        $xml->endElement();
        
        $xml->startElementNS('gd', 'email', null);
        $xml->writeAttribute('label', 'Personal');
        $xml->writeAttribute('address', $this->get_email());
        $xml->endElement();
        
        $xml->startElementNS('gd', 'phoneNumber', null);
        $xml->writeAttribute('label', 'Personal');
        $xml->text($this->get_phone_number());
        $xml->endElement();
        
        $xml->startElementNS('gContact', 'groupMembershipInfo', null);
        $xml->writeAttribute('href', 'some group');
        $xml->endElement();
        
        $xml->endElement();
        return $xml->outputMemory();
    }
}

