<?php

namespace Tests\AppBundle\Framework;

use PHPUnit\Framework\Exception;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    protected $client;

    protected $container;

    protected $em;

    protected $crawler;

    protected $response;

    protected $responseContent;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->client = static::createClient();

        $this->container = $this->client->getContainer();

        $this->em = $this->getDoctrine()->getManager();

    }

    protected function setUp()
    {
        parent::setUp();

        static $metadatas;

        if(! isset($metadatas))
        {
            $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        }

        $schemaTool = new SchemaTool($this->em);

        $schemaTool->dropDatabase();

        if(!empty($metadatas))
        {
            $schemaTool->createSchema($metadatas);
        }
    }

    protected function getParameter($param)
    {
        return $this->container->getParameter($param);
    }

    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    public function assertResponseOk()
    {
        $this->assertEquals(200, $this->response->getStatusCode()); ;

        return $this;
    }

    public function visit($uri)
    {
        $this->crawler = $this->client->request('GET', $uri);

        $this->response = $this->client->getResponse();

        $this->responseContent = $this->response->getContent();

        return $this;
    }

    public function seeText($text)
    {
        $this->assertContains($text, $this->responseContent) ;

        return $this;
    }

    protected function onNotSuccessfulTest(\Throwable $t)
    {
        if($this->crawler && $this->crawler->filter('.exception-message')->count() > 0)
        {
            $throwableclass = get_class($t);

            $message = $this->crawler->filter('.exception-message')->eq(0)->text();

            throw new $throwableclass($t->getMessage() . ' | ' . $message);
        }

        throw $t;
    }



    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
        $this->em=null;
    }
}
