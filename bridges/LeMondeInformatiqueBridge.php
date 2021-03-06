<?php
class LeMondeInformatiqueBridge extends FeedExpander {

    const MAINTAINER = "ORelio";
    const NAME = "Le Monde Informatique";
    const URI = "http://www.lemondeinformatique.fr/";
    const DESCRIPTION = "Returns the newest articles.";

    public function collectData(){
        $this->collectExpandableDatas(self::URI . 'rss/rss.xml', 10);
    }

    protected function parseItem($newsItem){
        $item = $this->parseRSS_1_0_Item($newsItem);
        $article_html = $this->getSimpleHTMLDOMCached($item['uri']) 
            or $this->returnServerError('Could not request LeMondeInformatique: ' . $item['uri']);
        $item['content'] = $this->CleanArticle($article_html->find('div#article', 0)->innertext);
        $item['title'] = $article_html->find('h1.cleanprint-title', 0)->plaintext;
        return $item;
    }

    function StripCDATA($string) {
        $string = str_replace('<![CDATA[', '', $string);
        $string = str_replace(']]>', '', $string);
        return $string;
    }

    function StripWithDelimiters($string, $start, $end) {
        while (strpos($string, $start) !== false) {
            $section_to_remove = substr($string, strpos($string, $start));
            $section_to_remove = substr($section_to_remove, 0, strpos($section_to_remove, $end) + strlen($end));
            $string = str_replace($section_to_remove, '', $string);
        } return $string;
    }

    function CleanArticle($article_html) {
        $article_html = $this->StripWithDelimiters($article_html, '<script', '</script>');
        $article_html = $this->StripWithDelimiters($article_html, '<h1 class="cleanprint-title"', '</h1>');
        return $article_html;
    }

    public function getCacheDuration() {
        return 1800; // 30 minutes
    }
}
