<?php

namespace App\Traits;

trait ApiResponser
{

	protected function success($data, string $message = 'OK', int $code = 200)
	{
		return response()->json([
			'code' => $code,
			'message' => $message,
			'data' => $data
		], $code);
	}

	//renew bacis customer response
	private function bacisCustomerProfile($customer)
	{
		return array_merge($this->bacisProfile($customer), array());
	}

	// Bacis profile using all worker and user
	private function bacisProfile($data)
	{
		return array(
			"id" => $data->id,
			"name" => $data->name,
			"first_name" => $data->first_name,
			"last_name" => $data->last_name,
			"address" => $data->address,
			"gender" => $data->gender,
			"phone" => $data->phone,
			"email" => $data->email,
			"status" => $data->status,
			"latitude" => $data->latitude,
			"longtitude" => $data->longtitude,
			"device_token" => $data->device_token
		);
	}

	protected function successWithPaginate($data, $meta = null, string $message = 'OK', int $code = 200)
	{
		return response()->json([
			'code' => $code,
			'message' => $message,
			'data' => $data,
			'meta' => [
				"current_page" => $meta['current_page'] ?? null,
				'per_page' => $meta['per_page'] ?? null,
				'next_page_url' => $meta['next_page_url'] ?? null,
				'prev_page_url' => $meta['prev_page_url'] ?? null,
				'to' => $meta['to'] ?? null,
				'total' => $meta['total'] ?? null
			]
		], $code);
	}

	protected function error(string $message = 'Server error', int $code = 500, $data = null)
	{
		return response()->json([
			'code' => $code,
			'message' => $message,
			'data' => $data
		], $code);
	}

	protected function badRequest(int $code = 400, string $status = "Bad Request", $message = 'Invalid')
	{
		return response()->json([
			"status" => $status,
			"code" => $code,
			"message" => $message
		], $code);
	}

	protected function forbidden(int $code = 403, string $status = "Forbidden", $message = "You don't have permission access to edit or update!")
	{
		return response()->json([
			"status" => $status,
			"code" => $code,
			"message" => $message
		], $code);
	}
}