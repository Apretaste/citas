<?php

use Apretaste\Request;
use Apretaste\Response;
use Framework\Database;

class Service
{
	/**
	 * Service entry point
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Alert
	 */
	public function _main(Request $request, Response $response)
	{
		return $this->_random($request, $response);
	}

	/**
	 * Display a random quote
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Alert
	 */
	public function _random(Request $request, Response $response)
	{
		// get the total numbers of quotes
		$totalQuotes = Database::queryCache("SELECT COUNT(id) AS cnt FROM _citas", Database::CACHE_MONTH)[0]->cnt;

		// get the quote
		$quoteId = rand(1, $totalQuotes);
		$quote = $this->getQuote($quoteId);

		// send information to the view
		$response->setTemplate("quote.ejs", $quote);
	}

	/**
	 * View an specific quote
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws FeedException
	 * @throws Alert
	 */
	public function _quote(Request $request, Response $response)
	{
		// get the ID of the quote
		$id = $request->input->data->id ?? false;

		// get the quote
		$quote = $this->getQuote($id);

		// if empty return an error
		if(empty($quote)) {
			return $response->setTemplate('message.ejs', [
				'header' => 'Cita perdida',
				'icon' => 'sentiment_very_dissatisfied',
				'text' => 'Tuvimos un problema encontrando la cita que busca. Es posible que no se encuentre en nuestra base de datos o la hallamos borrado. Por el momento, lea una aleatoria.',
				'btnLink' => 'CITAS random',
				'btnCaption' => 'Cita aleatoria']);
		}

		// send information to the view
		$response->setTemplate("quote.ejs", $quote);
	}

	/**
	 * Like a quote
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _like(Request $request, Response $response)
	{
		// get the quote ID 
		$id = $request->input->data->id ?? false;
		if(empty($id)) return false;

		// update the quote
		Database::queryFirst("UPDATE _citas SET likes = likes + 1 WHERE id = $id");
	}

	/**
	 * Get a quote based on the ID
	 *
	 * @param Int $id
	 * @return Object | false
	 */
	private function getQuote(Int $id)
	{
		// do not allow invalid inputs
		if(empty($id)) return false;

		// get the quote
		$quote = Database::queryFirst("SELECT * FROM _citas WHERE id = $id");

		// error if no quote was found
		if(empty($quote)) return false;

		// mark the quote as viewed
		Database::queryFirst("UPDATE _citas SET views = views + 1 WHERE id = $id");

		// return the quote
		return $quote;
	}
}
