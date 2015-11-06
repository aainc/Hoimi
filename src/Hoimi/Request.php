<?php
namespace Hoimi;
use Hoimi\Exception\ValidationException;

/**
 * Class Request
 *
 * instance of Request Header, Request body
 *
 * @package Hoimi
 */
class Request implements Gettable
{
    private $request = null;
    private $uri = null;
    private $urlParameter = null;
    private $headers = null;
    private $files = null;

    /**
     * @param $headers
     * @param $request
     * @param $files
     */
    public function __construct($headers, $request, $files = null)
    {
        $this->headers = $headers;
        $this->request = $request;
        $this->files   = $files;

        if (isset($this->headers['REQUEST_METHOD']) && $this->headers['REQUEST_METHOD'] === 'PUT') {
            $this->request['@PUT_BODY'] = rawurldecode(file_get_contents('php://input'));
        } elseif (isset($this->headers['REQUEST_METHOD']) && $this->headers['REQUEST_METHOD'] === 'DELETE') {
            $request = array();
            parse_str(file_get_contents('php://input'), $request);
            foreach ($request as $key => $val) $this->request[$key] = $val;
        }
    }

    public function getMethod ()
    {
        return isset($this->headers['REQUEST_METHOD']) ? $this->headers['REQUEST_METHOD'] : null;
    }

    /**
     * @return mixed|null
     */
    public function parseUrl()
    {
        if ($this->uri === null) {
            $this->uri = parse_url($this->getHeader('REQUEST_URI'));
        }
        return $this->uri;
    }

    /**
     * @param $key
     * @param null $default
     * @return null
     */
    public function get($key, $default = null)
    {
        return isset($this->request[$key]) ? $this->request[$key] :
            (isset($this->urlParameter[$key]) ? $this->urlParameter[$key] : $default);
    }

    public function getBody ($key = null)
    {
        return $key ? $this->request[$key] : $this->request;
    }

    /**
     * @param $key
     * @param null $default
     * @return null
     */
    public function getHeader($key, $default = null)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : $default;
    }

    /**
     * @return null
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $urlParameter
     */
    public function setUrlParameters(array $urlParameter)
    {
        $this->urlParameter = $urlParameter;
    }

    /**
     * @param $key
     * @param $val
     */
    public function setUrlParameter($key, $val)
    {
        $this->urlParameter[$key] = $val;
    }

    /**
     * @param $key
     * @param null $default
     * @return null
     */
    public function getUrlParameter($key, $default = null)
    {
        return isset($this->urlParameter[$key]) ? $this->urlParameter[$key] : $default;
    }

    /**
     * @param $key
     * @return UploadFile
     * @throws Exception\ValidationException
     */
    public function getFile ($key)
    {
        $result = null;
        if ($this->fileExists($key)){
            if (isset($this->files[$key][0])) {
                throw new ValidationException(array($key => 'TOO_MANY_FILES'));
            }
            if (!isset($this->files[$key]['uploaded'])) {
                $tempFileManager = new UploadFile(null, $this->files[$key]['name'], $this->files[$key]['type']);
                move_uploaded_file($this->files[$key]['tmp_name'], $tempFileManager->getPath());
                $this->files[$key]['uploaded'] = $tempFileManager;
            }
            $result = $this->files[$key]['uploaded'];
        }
        return $result;
    }

    /**
     * @param $key
     * @return UploadFile[]
     * @throws Exception\ValidationException
     */
    public function getFiles ($key)
    {
        $result = null;
        if ($this->fileExists($key)) {
            if (!isset($this->files[$key][0])) {
                throw new ValidationException(array($key => 'SINGLE_FILE'));
            }
            if (!isset($this->files[$key]['uploaded'])) {
                for ($i = 0, $count = count($this->files[$key]);$i < $count; $i++) {
                    $file = $this->files[$key][$i];
                    $tempFileManager = new UploadFile(null, $file['name'], $file['type']);
                    move_uploaded_file($file['tmp_name'], $tempFileManager->getPath());
                    $this->files[$key]['uploaded'][] = $tempFileManager;
                }
            }
            $result = $this->files[$key];
        }
        return $result;
    }

    /**
     * @param $key
     * @return bool
     */
    public function fileExists($key)
    {
        return isset($this->files) && isset($this->files[$key]);
    }

}
