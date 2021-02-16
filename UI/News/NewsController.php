<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\News;

use DateTimeImmutable;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class NewsController implements Controller
{
	public const PAGE_SLUG = 'co-je-noveho';
	public const PARAM_NEWS_ID = 'newsId';

	public function __construct(
		string $dateFormatHuman,
		string $dateFormatRobot,
		private Base $base,
		private Engine $latte,
	) {
		$this->latte->addFilter('forHumans', static fn(DateTimeImmutable $date) => $date->format($dateFormatHuman));
		$this->latte->addFilter('forRobots', static fn(DateTimeImmutable $date) => $date->format($dateFormatRobot));
	}


	public function render(): void
	{
		$newsId = get_query_var(self::PARAM_NEWS_ID);

		if ($newsId !== '') {
			$post = \get_post($newsId);
			if ($post === null) {
				// todo 404 if not found
				return;
			}

			$this->processSingle($post);
			return;
		}

		$this->processList();
	}

	private function processList(): void
	{
		$posts = get_posts(['post_type' => 'news']);

		$params = [
			'articles' => \array_map(static fn(\WP_Post $post): NewsDC => NewsDC::fromPost($post), $posts),
			'areAnyArticles' => \count($posts) > 0,
		];

		$this->latte->render(
			__DIR__ . '/NewsController.list.latte',
			\array_merge($this->base->getLayoutVariables('news'), $params),
		);
	}

	private function processSingle(\WP_Post $post): void
	{

		$params = [
			'article' => NewsDC::fromPost($post),
		];

		$this->latte->render(
			__DIR__ . '/NewsController.single.latte',
			\array_merge($this->base->getLayoutVariables('news'), $params),
		);
	}

}
