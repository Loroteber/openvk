<?php

declare(strict_types=1);

namespace openvk\Web\Models\Entities;

use openvk\Web\Models\RowModel;
use openvk\Web\Models\Entities\User;
use Chandler\Database\DatabaseConnection;
use Parsedown;

class FAQ extends RowModel
{
    
    protected $tableName = "known_issue";

    private $faqDir = OPENVK_ROOT . "/data/knowledgebase/faq";

    public function getTopicList(string $lang): array
    {
        $topics = [];

        foreach (chandler_parse_yaml($this->faqDir . "/topics.yml") as $url => $topic) {
            if (!isset($topic["name"][$lang])) {
                continue;
            }
            
            $topics[] = [
                "url"       => $url,
                "title"     => $topic["name"][$lang],
                "for_admin" => $topic["for_admin"]
            ];
        }

        return $topics;
    }

    public function getTopicPreviews(string $lang): array
    {
        $topics = [];

        foreach (chandler_parse_yaml($this->faqDir . "/topics.yml") as $name => $topic) {
            if (!isset($topic["name"][$lang])) { 
                continue;
            }
            
            if (file_exists("$this->faqDir/$name.$lang.md")) {
                $file = "$this->faqDir/$name.$lang.md";
            } elseif (file_exists("$this->faqDir/$name.md")) {
                $file = "$this->faqDir/$name.md";
            } else {
                continue;
            }

            $lines = file($file);
            $index = 1;
            $questions = [];

            foreach ($lines as $line) {
                if ($index === 4) {
                    break;
                }

                if (strpos($line, "# ") === 0) {
                    $questions[] = [
                        "title" => substr($line, 2),
                        "index" => $index
                    ];
                    ++$index;
                }
            }

            $topics[] = [
                "url"       => $name,
                "title"     => $topic["name"][$lang],
                "icon"      => $topic["icon"],
                "for_admin" => $topic["for_admin"],
                "questions" => $questions
            ];
        }

        return $topics;
    }

    public function getTopic(string $name, string $lang): array
    {
        $topic = [];

        $topics = chandler_parse_yaml($this->faqDir . "/topics.yml");
        if (!isset($topics[$name])) {
            return $topic;
        }

        if (file_exists("$this->faqDir/$name.$lang.md")) {
            $file = "$this->faqDir/$name.$lang.md";
        } elseif (file_exists("$this->faqDir/$name.md")) {
            $file = "$this->faqDir/$name.md";
        } else {
            return $topic;
        }

        $content = [];
        $lines   = file($file, FILE_IGNORE_NEW_LINES);
        $id      = 1;

        foreach ($lines as $line) {
            if (strpos($line, "# ") === 0) {
                $content[] = [
                    'id' => $id++,
                    'q'  => substr($line, 2),
                    'a'  => '',
                ];
            } elseif (!empty($content)) {
                $last = count($content) - 1;
                $content[$last]['a'] .= $line . "\n";
            }
        }

        foreach ($content as &$item) {
            $item['a'] = (new Parsedown())->text(trim($item['a']));
        }

        if (isset($topics[$name]["name"][$lang])) {
            $title = $topics[$name]["name"][$lang];
        } else {
            $title = $topics[$name]["name"]["_"];
        }

        $topic = [
            "title"     => $title,
            "url"       => $name,
            "icon"      => $topics[$name]["icon"],
            "for_admin" => $topics[$name]["for_admin"],
            "content"   => $content
        ];

        return $topic;
    }
}
