<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;

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
}
