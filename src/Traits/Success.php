<?php

namespace Songshenzong\Api\Traits;

use Illuminate\Http\JsonResponse;
use Songshenzong\Api\Exception\ApiException;

trait Success
{

    /**
     * Public Success Method.
     *
     * @param      $statusCode
     * @param      $message
     * @param null $data
     *
     * @return JsonResponse
     * @throws ApiException
     */
    public function success($statusCode, $message, $data = null)
    {


        if (null === $this->getStatusCode()) {
            $this->setStatusCode($statusCode);
        }

        if (null === $this->getMessage()) {
            $this->setMessage($message);
        }

        if (null === $this->getData()) {
            $this->setData($data);
        }


        $content['message'] = $this->getMessage();

        if (null !== $this->getCode()) {
            $content['code'] = $this->getCode();
        }

        $content['status_code'] = $this->getStatusCode();


        if (null !== $this->getData() && $this->getData() !== $this->getErrors()) {
            $content['data'] = $this->getData();
        }

        if (null !== $this->getErrors()) {
            $content['errors'] = $this->getErrors();
        }

        $content = $content + $this->Hypermedia;

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
     * @return mixed
     * @throws ApiException
     */
    public function ok($data = null)
    {
        return $this->success(200, 'OK', $data);
    }

    /**
     * Success - item
     *
     * @param null $data
     *
     * @return mixed
     * @throws ApiException
     */
    public function item($data = null)
    {
        return $this->ok($data);
    }

    /**
     * Success - collection
     *
     * @param null $data
     *
     * @return mixed
     * @throws ApiException
     */
    public function collection($data = null)
    {
        return $this->ok($data);
    }

    /**
     * @param null $data
     *
     * @return mixed
     * @throws ApiException
     */
    public function paginate($data = null)
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
     * @return mixed
     * @throws ApiException
     */
    public function created($message = 'Created', $data = null)
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
     * @return mixed
     * @throws ApiException
     */
    public function accepted($message = 'Accepted', $data = null)
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
     * @return mixed
     * @throws ApiException
     */
    public function nonAuthoritativeInformation($message = 'Non-Authoritative Information', $data = null)
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
     * @return mixed
     * @throws ApiException
     */
    public function noContent($message = 'No Content', $data = null)
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
     * @return mixed
     * @throws ApiException
     */
    public function resetContent($message = 'Reset Content', $data = null)
    {
        return $this->success(205, $message, $data);
    }
}
