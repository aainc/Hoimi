<?php
namespace Hoimi\Response;

use Hoimi\Response;

abstract class Html implements Response
{
    public function getHeaders()
    {
        return array('ContentType: text/html; charset=UTF-8');
    }

    public function getContent ()
    {
        ob_start();
        include $this->getTemplatePath();
        return ob_get_clean();
    }

    public abstract function getTemplatePath();

    public function assign($word)
    {
        echo htmlspecialchars($word, ENT_QUOTES);
    }

    public function writeHtml($html)
    {
        echo $html;
    }

    public function assignUrl($word)
    {
        echo urlencode($word);
    }
}
