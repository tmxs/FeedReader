<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class OpmlImportService
{
    public function importFeedSource()
    {
        $opmlFile = simplexml_load_file('feedlist.opml');
        foreach($opmlFile->body->outline AS $category) {
          echo $category['title'] . ":" . "\n";
            foreach($category->outline AS $categoryItems) {
              echo "     " . $categoryItems['title'] . "\n";
            }
        }
    }

    public function getRSSData()
    {
        foreach ($this->feedData->channel->item as $post) {
            if (strtotime($post->pubDate) > strtotime("-7 days")) {
                $feedPost = new Post();
                $feedPost->setTitle($post->title);
                $feedPost->setAuthor($this->feedData->channel->title);
                $feedPost->setDate(new \DateTime('@'. strtotime($post->pubDate)));
                $feedPost->setFeed($this->feedData->channel->title);
                $feedPost->setUrl($post->link);
                $feedPost->setThumbnailUrl($this->getThumbnailUrl($post->description, $post->link));
                $feedPost->setTeaser($this->getWordsFromString($post->description, 30));
                $feedPost->setStatus(true);
                $feedPost->setSave(true);

                $this->posts[strtotime($post->pubDate)] = $feedPost;
            }
        }
    }
}
