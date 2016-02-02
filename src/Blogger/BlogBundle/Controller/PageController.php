<?php
// src/Blogger/BlogBundle/Controller/PageController.php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Entity\Enquiry;
use Blogger\BlogBundle\Form\EnquiryType;

class PageController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();

        $blogs = $em->getRepository('BloggerBlogBundle:Blog')
                    ->getLatestBlogs();

        return $this->render('BloggerBlogBundle:Page:index.html.twig', array(
            'blogs' => $blogs,
        ));
    }
     public function aboutAction()
    {
        return $this->render('BloggerBlogBundle:Page:about.html.twig');
    }
    public function contactAction()
	{
	    $enquiry = new Enquiry();
	    $form = $this->createForm(new EnquiryType(), $enquiry);

	    $request = $this->getRequest();
	    if ($request->getMethod() == 'POST') {
	        $form->bind($request);

	        if ($form->isValid()) {
	            // Perform some action, such as sending an email

	            // Redirect - This is important to prevent users re-posting
	            // the form if they refresh the page
	            return $this->redirect($this->generateUrl('BloggerBlogBundle_contact'));
	        }
	    }

	    return $this->render('BloggerBlogBundle:Page:contact.html.twig', array(
	        'form' => $form->createView()
	    ));
	    
	    if ($form->isValid()) {

            $message = \Swift_Message::newInstance()
                ->setSubject('Contact enquiry from symblog')
                ->setFrom('enquiries@symblog.co.uk')
                ->setTo($this->container->getParameter('blogger_blog.emails.contact_email'))
                ->setBody($this->renderView('BloggerBlogBundle:Page:contactEmail.txt.twig', array('enquiry' => $enquiry)));
            $this->get('mailer')->send($message);

            $this->get('session')->setFlash('blogger-notice', 'Your contact enquiry was successfully sent. Thank you!');

            // Redirect - This is important to prevent users re-posting
            // the form if they refresh the page
            return $this->redirect($this->generateUrl('BloggerBlogBundle_contact'));
        }
	}
   
    // Get remote file contents, preferring faster cURL if available
    function remote_get_contents($url)
    {
        $username   = 'labroots_nathan';
        $password   = 'wwvvlqhc7tfmpl';
        $email      = $this->container->getParameter('blogger_blog.emails.contact_email');
        $api_url    = 'http://api.verify-email.org/api.php?';
                
        $url        = $api_url . 'usr=' . $username . '&pwd=' . $password . '&check=' . $email;

        $object     = json_decode(remote_get_contents($url)); // the response is received in JSON format; here we use the function remote_get_contents($url) to detect in which way to get the remote content
        
        echo 'The email address ' . $email . ' is ' . ($object->verify_status?'GOOD':'BAD or cannot be verified') . '  '; 
        echo 'authentication_status - ' . $object->authentication_status . ' (your authentication status: 1 - success; 0 - invalid user)'; 
        echo 'limit_status - ' . $object->limit_status . ' (1 - verification is not allowed, see limit_desc; 0 - not limited)'; 
        echo 'limit_desc - ' . $object->limit_desc . ' '; 
        echo 'verify_status - ' . $object->verify_status . ' (entered email is: 1 - OK; 0 - BAD)'; 
        echo 'verify_status_desc - ' . $object->verify_status_desc . ' ';

            if (function_exists('curl_get_contents') AND function_exists('curl_init'))
            {
                    return curl_get_contents($url);
            }
            else
            {
                    // A litte slower, but (usually) gets the job done
                    return file_get_contents($url);
            }
    }

    function curl_get_contents($url)
    {
            // Initiate the curl session
            $ch = curl_init();
            
            // Set the URL
            curl_setopt($ch, CURLOPT_URL, $url);
            
            // Removes the headers from the output
            curl_setopt($ch, CURLOPT_HEADER, 0);
            
            // Return the output instead of displaying it directly
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            
            // Execute the curl session
            $output = curl_exec($ch);
            
            // Close the curl session
            curl_close($ch);
            
            // Return the output as a variable
            return $output;
    }
    public function sidebarAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();

        $tags = $em->getRepository('BloggerBlogBundle:Blog')
                   ->getTags();

        $tagWeights = $em->getRepository('BloggerBlogBundle:Blog')
                         ->getTagWeights($tags);
                         
        $commentLimit   = $this->container
                               ->getParameter('blogger_blog.comments.latest_comment_limit');
        $latestComments = $em->getRepository('BloggerBlogBundle:Comment')
                             ->getLatestComments($commentLimit);

        return $this->render('BloggerBlogBundle:Page:sidebar.html.twig', array(
            'latestComments'    => $latestComments,
            'tags' => $tagWeights
        ));
    }
}