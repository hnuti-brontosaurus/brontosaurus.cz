<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use HnutiBrontosaurus\LegacyBisApiClient\Request\Adoption;
use HnutiBrontosaurus\LegacyBisApiClient\Request\EventAttendee;
use HnutiBrontosaurus\LegacyBisApiClient\Request\EventParameters;
use HnutiBrontosaurus\LegacyBisApiClient\Request\OrganizationalUnitParameters;
use HnutiBrontosaurus\LegacyBisApiClient\Request\Parameters;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Event\Event;
use HnutiBrontosaurus\LegacyBisApiClient\Response\InvalidContentTypeException;
use HnutiBrontosaurus\LegacyBisApiClient\Response\InvalidParametersException;
use HnutiBrontosaurus\LegacyBisApiClient\Response\InvalidUserInputException;
use HnutiBrontosaurus\LegacyBisApiClient\Response\InvalidXMLStructureException;
use HnutiBrontosaurus\LegacyBisApiClient\Response\OrganizationalUnit\OrganizationalUnit;
use HnutiBrontosaurus\LegacyBisApiClient\Response\OrganizationalUnit\UnknownOrganizationUnitTypeException;
use HnutiBrontosaurus\LegacyBisApiClient\Response\Response;
use HnutiBrontosaurus\LegacyBisApiClient\Response\ResponseErrorException;
use HnutiBrontosaurus\LegacyBisApiClient\Response\UnauthorizedAccessException;
use HnutiBrontosaurus\LegacyBisApiClient\Response\UnknownErrorException;
use Psr\Http\Message\ResponseInterface;


final class Client
{

	private HttpClient $httpClient;
	private string $url;
	private string $username;
	private string $password;


	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $url, string $username, string $password, HttpClient $httpClient)
	{
		if ($url === '') {
			throw new InvalidArgumentException('You need to pass an URL with BIS API.');
		}
		if ($username === '') {
			throw new InvalidArgumentException('Username is required for authenticating against BIS.');
		}
		if ($password === '') {
			throw new InvalidArgumentException('Password is required for authenticating against BIS.');
		}

		$this->url = \rtrim($url, '/');
		$this->username = $username;
		$this->password = $password;
		$this->httpClient = $httpClient;
	}


	// events

	/**
	 * @throws NotFoundException
	 * @throws TransferErrorException
	 * @throws ResponseErrorException
	 */
	public function getEvent(int $id, EventParameters $params = null): Event
	{
		$params = ($params !== null ? $params : new EventParameters());
		$params->setId($id);
		$response = $this->processRequest($params);

		$data = $response->getData();

		if (\count($data) === 0) {
			throw new NotFoundException('No result for event with id `' . $id . '`.');
		}

		return Event::fromResponseData(\reset($data));
	}


	/**
	 * @return Event[]
	 * @throws NotFoundException
	 * @throws TransferErrorException
	 * @throws ResponseErrorException
	 */
	public function getEvents(EventParameters $params = null): array
	{
		$response = $this->processRequest($params !== null ? $params : new EventParameters());
		$data = $response->getData();

		if ($data === null) {
			return [];
		}

		return \array_map(Event::class . '::fromResponseData', $data);
	}


	/**
	 * @throws ResponseErrorException
	 */
	public function addAttendeeToEvent(EventAttendee $eventAttendee): void
	{
		$eventAttendee->setCredentials($this->username, $this->password);
		$response = $this->httpClient->send($this->createRequest($eventAttendee));

		$this->checkForResponseContentType($response);

		$domDocument = $this->generateDOM($response);

		$this->checkForResponseErrors($domDocument);
	}


	// organizational units

	/**
	 * @return OrganizationalUnit[]
	 * @throws NotFoundException
	 * @throws TransferErrorException
	 * @throws ResponseErrorException
	 */
	public function getOrganizationalUnits(OrganizationalUnitParameters $params = null): array
	{
		$response = $this->processRequest($params !== null ? $params : new OrganizationalUnitParameters());

		$organizationalUnits = [];
		foreach ($response->getData() as $organizationalUnit) {
			try {
				$organizationalUnits[] = OrganizationalUnit::fromResponseData($organizationalUnit);

			} catch (UnknownOrganizationUnitTypeException $e) {
				continue; // In case of unknown type - just ignore it.

			}
		}

		return $organizationalUnits;
	}


	// adoption

	/**
	 * @throws ResponseErrorException
	 */
	public function saveRequestForAdoption(Adoption $adoption): void
	{
		$adoption->setCredentials($this->username, $this->password);

		$response = $this->httpClient->send($this->createRequest($adoption));

		$this->checkForResponseContentType($response);

		$domDocument = $this->generateDOM($response);

		$this->checkForResponseErrors($domDocument);
	}


	/**
	 * @throws NotFoundException
	 * @throws TransferErrorException
	 * @throws ResponseErrorException
	 */
	private function processRequest(Parameters $requestParameters): Response
	{
		$requestParameters->setCredentials($this->username, $this->password);

		try {
			$response = $this->httpClient->send($this->createRequest($requestParameters));

		} catch (ClientException $e) {
			throw new NotFoundException('Bis client could not find the queried resource.', 0, $e);

		} catch (GuzzleException $e) {
			throw new TransferErrorException('Unable to process request: transfer error.', 0, $e);
		}

		$this->checkForResponseContentType($response);

		$domDocument = $this->generateDOM($response);
		$this->checkForResponseErrors($domDocument);

		return new Response($response, $domDocument);
	}


	/**
	 * @throws InvalidContentTypeException
	 */
	private function checkForResponseContentType(ResponseInterface $response): void
	{
		if (\strncmp($response->getHeaderLine('Content-Type'), 'text/xml', \strlen('text/xml')) !== 0) {
			throw new InvalidContentTypeException('Unable to process response: the response Content-Type is invalid or missing.');
		}
	}


	/**
	 * @throws InvalidXMLStructureException
	 */
	private function generateDOM(ResponseInterface $response): \DOMDocument
	{
		try {
			$domDocument = new \DOMDocument();
			$domDocument->loadXML($response->getBody()->getContents());

		} catch (\Exception $e) {
			throw new InvalidXMLStructureException('Unable to process response: response body contains invalid XML.', 0, $e);
		}

		return $domDocument;
	}


	/**
	 * @throws ResponseErrorException
	 */
	private function checkForResponseErrors(\DOMDocument $domDocument): void
	{
		$resultNode = $domDocument->getElementsByTagName(Response::TAG_RESULT)->item(0);
		\assert($resultNode instanceof \DOMElement);

		if ($resultNode->hasAttribute(Response::TAG_RESULT_ATTRIBUTE_ERROR)) {
			switch ($resultNode->getAttribute(Response::TAG_RESULT_ATTRIBUTE_ERROR)) {
				case 'success': // In case of POST request with form data, BIS returns `<result error="success" />` for some reason... Let's pretend that there is no error in such case because... you know... there is no error!
					break;

				case 'user':
					throw new InvalidUserInputException($resultNode);

				case 'forbidden':
					throw new UnauthorizedAccessException();

				case 'params':
					throw new InvalidParametersException();

				default:
					throw new UnknownErrorException($resultNode->getAttribute(Response::TAG_RESULT_ATTRIBUTE_ERROR));
			}
		}
	}


	private function createRequest(Parameters $parameters): Request
	{
		return new Request(
			'POST',
			$this->url,
			[
				'Content-Type' => 'application/x-www-form-urlencoded',
			],
			\http_build_query($parameters->getAll())
		);
	}

}
