<?php

namespace App\Service;

use App\Feed\Post;
use App\Entity\Feed;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class: FeedReaderService
 *
 */
class FeedReaderService
{
    public $posts = array();
    private $feedData;

    //TODO: Daten in den PHP-Cache schreiben und nur nach neuen suchen

    /**
     * getFeedData
     *
     * @param EntityManagerInterface $entityManager
     */
    public function getFeedData(EntityManagerInterface $entityManager)
    {
        //TODO: Detect type by html header
        $feedSources = $entityManager->createQuery('SELECT feed.url FROM App\Entity\Feed feed')->getResult();
        foreach ($feedSources as $feed) {
            $this->feedData = simplexml_load_file($feed['url']);
            if ($this->feedData->getName()  == 'feed') {
                $this->getAtomData();
            } elseif ($this->feedData->getName()  == 'rss') {
                $this->getRSSData();
            }
        }
        krsort($this->posts);
        return $this->posts;
    }

    /**
     * getRSSData
     *
     */
    public function getRSSData()
    {
        foreach ($this->feedData->channel->item as $post) {
            if (strtotime($post->pubDate) > strtotime("-1 days")) {
                $feedPost = new Post();
                $feedPost->setTitle($post->title);
                $feedPost->setAuthor($this->feedData->channel->title);
                $feedPost->setDate(new \DateTime('@'. strtotime($post->pubDate)));
                $feedPost->setFeed($this->feedData->channel->title);
                $feedPost->setUrl($post->link);
                $feedPost->setThumbnailUrl($this->getThumbnailUrl($post->link));
                $feedPost->setTeaser($this->getWordsFromString($post->description, 30));
                $feedPost->setStatus(true);
                $feedPost->setSave(true);

                $this->posts[strtotime($post->pubDate)] = $feedPost;
            }
        }
    }

    /**
     * getAtomData
     *
     */
    public function getAtomData()
    {
        foreach ($this->feedData->entry as $post) {
            if (strtotime($post->published) > strtotime("-1 day")) {
                $feedPost = new Post();
                $feedPost->setTitle($post->title);
                $feedPost->setAuthor($this->feedData->title);
                $feedPost->setDate(new \DateTime('@'.strtotime($post->published)));
                $feedPost->setFeed($this->feedData->title);
                $feedPost->setUrl($post->link['href']);
//                if (strpos($this->feedData->author->uri,"youtube.com") !== false) {
////                    $feedPost->setThumbnailUrl()
//                } else {
                    $feedPost->setThumbnailUrl($this->getThumbnailUrl($post->link['href']));
//                }
                $feedPost->setTeaser($post->summary);
                $feedPost->setStatus(true);
                $feedPost->setSave(true);

                $this->posts[strtotime($post->published)] = $feedPost;
            }
        }
    }

    /**
     * getThumbnailUrl
     *
     * @param mixed $url
     */
    public function getThumbnailUrl($url)
    {
        libxml_use_internal_errors(true);
        $doc = new \DomDocument();
        $doc->loadHTML(\file_get_contents($url));
        $xpath = new \DOMXPath($doc);

        foreach ($xpath->query("//meta[@property='og:image']") as $ogImage) {
            return $ogImage->getAttribute('content');
        }
        // Alternative über im Feed hinterlegtes Bild - jedoch nicht immer gegeben
      // $dom = new \DomDocument();
      // @$dom->loadHTML($content);
      //   foreach ($dom->getElementsByTagName('img') as $item) {
      //       return $item->getAttribute('src');
      //   }
    }

    /**
     * getWordsFromString
     *
     * @param mixed $text
     * @param mixed $numberOfWords
     */
    public function getWordsFromString($text, $numberOfWords)
    {
        if ($text != null) {
            $words = explode(" ", $text);
            if (count($words) > $numberOfWords) {
                return implode(" ", array_slice($words, 0, $numberOfWords)) . "...";
            } else {
                return $text;
            }
        }
        return "";
    }
}
