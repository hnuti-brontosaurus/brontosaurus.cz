<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Preview;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class PreviewController implements Controller
{

	public function __construct(
		private Base $base,
		private Engine $latte,
		private \WP_Post $post,
	) {}

	public function render(): void
	{
		$params = [
			'title' => $this->post->post_title,
			'content' => $this->post->post_content,
		];

		$this->latte->render(
			__DIR__ . '/PreviewController.latte',
			\array_merge($this->base->getLayoutVariables('preview'), $params),
		);
	}
}
