<?php
namespace Hoimi;
/**
 * Interface Bindable
 * @package Hoimi
 */
interface Bindable
{
    /**
     * invoke at $session->bind(bindable).
     * receive a content that is binding to $_SESSION
     * @param array $content
     */
    public function setSessionContent(array $content);

    /**
     * @return String return A key that is used to binding to $_SESSION
     */
    public function getSessionKey();


    /**
     * invoke at $session->flush();
     * return a content that is wanted to bind to $_SESSION
     * @return array content
     */
    public function getSessionContent();
}