<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Response;

use Psr\Http\Message\ResponseInterface;


final class Response
{

	public const TAG_RESULT = 'result';
	public const TAG_RESULT_ATTRIBUTE_ERROR = 'error';
	private const TAG_ATTRIBUTE_ID = 'id';

	private ResponseInterface $httpResponse;
	private array $data;


	public function __construct(ResponseInterface $httpResponse, \DOMDocument $domDocument)
	{
		$this->httpResponse = $httpResponse;

		$this->parseDom($domDocument);
	}


	private function parseDom(\DOMDocument $domDocument): void
	{
		$domFinder = new \DOMXPath($domDocument);
		$rowNodeList = $domFinder->query('*', $domDocument->getElementsByTagName(self::TAG_RESULT)->item(0));

		$this->data = [];
		foreach ($rowNodeList as $rowNode) {
			\assert($rowNode instanceof \DOMElement);

			$row = [];
			foreach ($domFinder->query('*', $rowNode) as $node) {
				\assert($node instanceof \DOMElement);

				// if there is an ID attribute, use this one a the value as it is numeric representation (thus more technically reliable) of element's content
				$row[$node->nodeName] = $node->hasAttribute(self::TAG_ATTRIBUTE_ID) ?
					$node->getAttribute(self::TAG_ATTRIBUTE_ID)
					:
					$node->nodeValue;
			}

			$this->data[] = $row;
		}
	}


	public function getHttpResponse(): ResponseInterface
	{
		return $this->httpResponse;
	}


	public function getData(): array
	{
		return $this->data;
	}

}
