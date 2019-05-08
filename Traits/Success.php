<?php

namespace Songshenzong\Api\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait Success
 *
 * @package Songshenzong\Api\Traits
 */
trait Success
{

    /**
     * @var mixed
     */
    protected $data;


    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Public Success Method.
     *
     * @param  int    $statusCode
     * @param  string $message
     * @param null    $data
     *
     * @return JsonResponse
     * @throws \Songshenzong\Api\Exception\ApiException
     */
    public function success(int $statusCode, string $message, $data = null): JsonResponse
    {

        $this->setStatusCode($statusCode);

        $this->setMessage($message);

        $this->setData($data);


        $content['message'] = $this->getMessage();


        if (null !== $this->getCode()) {
            $content['code'] = $this->getCode();
        }


        $content['status_code'] = $this->getStatusCode();


        if (null !== $this->getData() && $this->getErrors() !== $this->getData()) {
            $content['data'] = $this->getData();
        }

        if (null !== $this->getErrors()) {
            $content['errors'] = $this->getErrors();
        }

        $content += $this->Hypermedia;

        return new JsonResponse($content, $this->getHttpStatusCode() ?: $this->getStatusCode());
    }

    /**
     * Success - OK
     *
     * Standard response for successful HTTP requests. The actual response will depend on the request method used. In a
     * GET request, the response will contain an entity corresponding to the requested resource. In a POST request, the
     * response will contain an entity describing or containing the result of the action.
     *
     * @param null $data
     *
     * @return JsonResponse
     * @throws \Songshenzong\Api\Exception\ApiException
     */
    public function ok($data = null): JsonResponse
    {
        return $this->success(200, 'OK', $data);
    }

    /**
     * @param null $data
     *
     * @return JsonResponse
     * @throws \Songshenzong\Api\Exception\ApiException
     */
    public function item($data = null): JsonResponse
    {
        return $this->ok($data);
    }

    /**
     * @param null $data
     *
     * @return JsonResponse
     * @throws \Songshenzong\Api\Exception\ApiException
     */
    public function collection($data = null): JsonResponse
    {
        return $this->ok($data);
    }

    /**
     * @param null $data
     *
     * @return JsonResponse
     * @throws \Songshenzong\Api\Exception\ApiException
     */
    public function paginate($data = null): JsonResponse
    {
        return $this->ok($data);
    }

    /**
     * Success - Created
     *
     * The request has been fulfilled, resulting in the creation of a new resource.
     *
     * @param string $message
     * @param null   $data
     *
     * @return JsonResponse
     * @throws \Songshenzong\Api\Exception\ApiException
     */
    public function created($message = 'Created', $data = null): JsonResponse
    {
        return $this->success(201, $message, $data);
    }

    /**
     * Success - Accepted
     *
     * The request has been accepted for processing, but the processing has not been completed. The request might or
     * might not be eventually acted upon, and may be disallowed when processing occurs.
     *
     * @param string $message
     * @param null   $data
     *
     * @return JsonResponse
     * @throws \Songshenzong\Api\Exception\ApiException
     */
    public function accepted($message = 'Accepted', $data = null): JsonResponse
    {
        return $this->success(202, $message, $data);
    }

    /**
     * Success - Non-Authoritative Information
     *
     * The server is a transforming proxy (e.g. a Web accelerator) that received a 200 OK from its origin, but is
     * returning a modified version of the origin's response.
     *
     * @param string $message
     * @param null   $data
     *
     * @return JsonResponse
     * @throws \Songshenzong\Api\Exception\ApiException
     */
    public function nonAuthoritativeInformation($message = 'Non-Authoritative Information', $data = null): JsonResponse
    {
        return $this->success(203, $message, $data);
    }

    /**
     * Success - No Content
     *
     * The server successfully processed the request and is not returning any content.
     *
     * @param string $message
     * @param null   $data
     *
     * @return JsonResponse
     * @throws \Songshenzong\Api\Exception\ApiException
     */
    public function noContent($message = 'No Content', $data = null): JsonResponse
    {
        return $this->success(204, $message, $data);
    }


    /**
     * Success - Reset Content
     *
     * The server successfully processed the request, but is not returning any content. Unlike a 204 response, this
     * response requires that the requester reset the document view.
     *
     * @param string $message
     * @param null   $data
     *
     * @return JsonResponse
     * @throws \Songshenzong\Api\Exception\ApiException
     */
    public function resetContent($message = 'Reset Content', $data = null): JsonResponse
    {
        return $this->success(205, $message, $data);
    }
}
