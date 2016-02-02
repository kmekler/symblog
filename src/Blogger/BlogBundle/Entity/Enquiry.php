<?php
// src/Blogger/BlogBundle/Entity/Enquiry.php

namespace Blogger\BlogBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Blogger\BlogBundle\Controller\PageController;

class Enquiry
{
    protected $name;

    protected $email;

    protected $subject;

    protected $body;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $username   = 'labroots_nathan';
        $password   = 'wwvvlqhc7tfmpl';
        $email      = 'email';
        $api_url    = 'http://api.verify-email.org/api.php?';
                
        $url        = $api_url . 'usr=' . $username . '&pwd=' . $password . '&check=' . $email;

        $object     = json_decode($url); 
        $metadata->addPropertyConstraint('name', new NotBlank());
        dump($object);
        $metadata->addPropertyConstraint('email', new Email(), $object == 1);

        $metadata->addPropertyConstraint('subject', new NotBlank());
        $metadata->addPropertyConstraint('subject', new Length(array('max' => 50)));
        $metadata->addPropertyConstraint('body', new Length(array('min' => 50)));
    }
}