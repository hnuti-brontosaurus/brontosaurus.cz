<?php

use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\OpportunityNotFound;
use HnutiBrontosaurus\Theme\Container;
use Tracy\Debugger;


/** @var Container $hb_container defined in functions.php */

$hb_bisApiClient = $hb_container->getBisClient();
$hb_dateFormatForHuman = $hb_container->getDateFormatForHuman();
$hb_dateFormatForRobot = $hb_container->getDateFormatForRobot();

// fix rank math tags
// remove generated meta data on this page, see https://support.rankmath.com/ticket/is-there-anyway-to-disable-or-remove-specific-meta-from-posts/
// https://rankmath.com/kb/filters-hooks-api-developer/#change-the-title
add_filter('rank_math/frontend/title', static fn($title) => null);
// https://rankmath.com/kb/filters-hooks-api-developer/#remove-opengraph-tags
add_action('rank_math/head', function () {
    remove_all_actions('rank_math/opengraph/facebook');
    remove_all_actions('rank_math/opengraph/twitter');
});

try {
	$hasBeenUnableToLoad = false;
	$opportunityId = (int) get_query_var('opportunityId');
	$opportunity = $hb_bisApiClient->getOpportunity($opportunityId);

	// add event name to title tag (source https://stackoverflow.com/a/62410632/3668474)
    add_filter(
        'document_title_parts',
        fn(array $title) => array_merge($title, ['title' => $opportunity->name]),
    );

} catch (OpportunityNotFound) {
	get_template_part('template-parts/content/content', 'error');
	get_footer();
	exit;

} catch (ConnectionToBisFailed $e) {
	$hasBeenUnableToLoad = true;
	Debugger::log($e);
}
?>

<main role="main">
	<article class="prilezitost">
		<?php if ($hasBeenUnableToLoad): ?>
			<div class="noResults hb-mbe-7">
				Promiňte, zrovna nám vypadl systém, kde máme uloženy všechny informace o plánovaných akcích.
				Zkuste to prosím za chvilku znovu.
			</div>
		<?php else: ?>
			<h1>
				<span class="hb-mie-3"><?php echo $opportunity->name; ?></span>

				<span class="prilezitost__labels hb-eventLabels">
					<span class="prilezitost__label hb-eventLabels__item">
						<?php echo hb_opportunityCategoryToString($opportunity->category); ?>
					</span>
				</span>
			</h1>

			<div class="prilezitost__top">
				<a class="prilezitost__image" href="<?php echo $opportunity->image->mediumSizePath; ?>">
					<img src="<?php echo $opportunity->image->mediumSizePath; ?>" alt="">
				</a>

				<dl class="prilezitost__basic">
					<dt>Datum</dt>
					<dd>
						<time datetime="<?php echo DateTimeImmutable::createFromInterface($opportunity->startDate)->format($hb_dateFormatForRobot); ?>">
							<?php echo hb_dateSpan(DateTimeImmutable::createFromInterface($opportunity->startDate), DateTimeImmutable::createFromInterface($opportunity->endDate), $hb_dateFormatForHuman); ?>
						</time>
					</dd>

					<dt>Místo</dt>
					<dd>
						<?php $location = $opportunity->location; ?>
						<?php $coordinates = $location->coordinates; ?>
						<?php if ($coordinates !== null): ?>
							<a href="https://mapy.cz/zakladni?q=<?php echo $coordinates; ?>" rel="noopener noreferrer" target="_blank">
								<?php echo $location->name; ?>
							</a>
						<?php else: ?>
							<?php echo $location->name; ?>
						<?php endif; ?>
					</dd>

					<dt>Kontakt</dt>
					<dd>
						<?php echo $opportunity->contactPerson->name; ?><br>
						<?php if ($opportunity->contactPerson->phoneNumber !== null && $opportunity->contactPerson->phoneNumber !== ''): ?>
						<a class="detail__basicInformation-contact" href="tel:<?php echo $opportunity->contactPerson->phoneNumber; ?>" rel="noopener noreferrer" target="_blank"><?php echo $opportunity->contactPerson->phoneNumber; ?></a><br>
						<?php endif; ?>
						<a class="detail__basicInformation-contact" href="mailto:<?php echo $opportunity->contactPerson->emailAddress; ?>" rel="noopener noreferrer" target="_blank"><?php echo $opportunity->contactPerson->emailAddress; ?></a>
					</dd>
				</dl>
			</div>

			<section>
				<?php echo $opportunity->introduction; ?>
			</section>

			<!--section 2-->
			<section>
				<h2>Popis činnosti</h2>
				<?php echo $opportunity->description; ?>
			</section>

			<!--section 3-->
			<?php if ($opportunity->locationBenefits !== null): ?>
			<section>
				<h2>Přínos pro lokalitu</h2>
				<?php echo $opportunity->locationBenefits; ?>
			</section>
			<?php endif; ?>

			<!--section 4-->
			<section>
				<h2>Přínos ze spolupráce</h2>
				<?php echo $opportunity->personalBenefits; ?>
			</section>

			<!--section 5-->
			<section>
				<h2>Požadavky</h2>
				<?php echo $opportunity->requirements; ?>
			</section>

			<!--section 6-->
			<section>
				<h2>Komu se ozvat?</h2>
				<address class="hb-fst-n">
					<dl class="prilezitost__contact">
						<dt>Kontaktní osoba:</dt>
						<dd><?php echo $opportunity->contactPerson->name; ?></dd>
						<dt>E-mail:</dt>
						<dd><a class="prilezitost__email" href="mailto:<?php echo $opportunity->contactPerson->emailAddress; ?>" rel="noopener noreferrer" target="_blank"><?php echo $opportunity->contactPerson->emailAddress; ?></a></dd>
						<?php if ($opportunity->contactPerson->phoneNumber !== null && $opportunity->contactPerson->phoneNumber !== ''): ?>
						<dt>Telefon:</dt>
						<dd><a class="prilezitost__phone" href="tel:<?php echo $opportunity->contactPerson->phoneNumber; ?>" rel="noopener noreferrer" target="_blank"><?php echo $opportunity->contactPerson->phoneNumber; ?></a></dd>
						<?php endif; ?>
					</dl>
				</address>
			</section>
		<?php endif; ?>
	</article>
</main>
