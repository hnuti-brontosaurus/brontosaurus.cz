<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Contacts;

use HnutiBrontosaurus\Theme\UI\AboutStructure\AboutStructureController;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class ContactsController implements Controller
{
	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}

	public function render(): void
	{
		$posts = \get_posts(['post_type' => 'contacts']);

		$params = [
			'aboutStructurePageLink' => $this->base->getLinkFor(AboutStructureController::PAGE_SLUG),
			'contacts' => \array_map(function (\WP_Post $post) {
				$thumbnail = get_the_post_thumbnail_url($post);
				$thumbnail = $thumbnail === false ? null : $thumbnail; // convert false to null which makes more sense

				$emailAddresses = \explode("\n", get_post_meta($post->ID, 'contacts_email', single: true));
				if ($emailAddresses === false || $emailAddresses[0] === '') {
					$emailAddresses = [];
				}

				return ContactDC::from(
					$post->post_title,
					$thumbnail !== null,
					$thumbnail,
					get_post_meta($post->ID, 'contacts_role', single: true),
					get_post_meta($post->ID, 'contacts_about', single: true),
					$emailAddresses,
				);
			}, $posts),
		];

		$this->latte->render(
			__DIR__ . '/ContactsController.latte',
			\array_merge($this->base->getLayoutVariables('contacts'), $params),
		);
	}

}
