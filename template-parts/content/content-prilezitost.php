<?php

use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\OpportunityNotFound;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\Rewrites\Opportunity;


/** @var Container $hb_container defined in functions.php */

$hb_bisApiClient = $hb_container->getBisClient();
$hb_dateFormatForHuman = $hb_container->getDateFormatForHuman();
$hb_dateFormatForRobot = $hb_container->getDateFormatForRobot();

try {
	$hasBeenUnableToLoad = false;
	$opportunityId = (int) get_query_var(Opportunity::HB_OPPORTUNITY_ID);
	$opportunity = $hb_bisApiClient->getOpportunity($opportunityId);

} catch (OpportunityNotFound) {
	get_template_part('template-parts/content/content', 'error');
	get_footer();
	exit;

} catch (ConnectionToBisFailed) {
	$hasBeenUnableToLoad = true;
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
				<span class="hb-mie-3"><?php echo $opportunity->getName(); ?></span>

				<span class="prilezitost__labels eventLabels">
					<span class="prilezitost__label eventLabels__item">
						<?php echo hb_opportunityCategoryToString($opportunity->getCategory()); ?>
					</span>
				</span>
			</h1>

			<div class="prilezitost__top">
				<a class="prilezitost__image" href="<?php echo $opportunity->getImage()->getMediumSizePath(); ?>">
					<img src="<?php echo $opportunity->getImage()->getMediumSizePath(); ?>" alt="">
				</a>

				<dl class="prilezitost__basic">
					<dt>Datum</dt>
					<dd>
						<time datetime="<?php echo $opportunity->getStartDate()->toNativeDateTimeImmutable()->format($hb_dateFormatForRobot); ?>">
							<?php echo hb_dateSpan($opportunity->getStartDate(), $opportunity->getEndDate(), $hb_dateFormatForHuman); ?>
						</time>
					</dd>

					<dt>Místo</dt>
					<dd>
						<?php $location = $opportunity->getLocation(); ?>
						<?php $coordinates = $location->getCoordinates(); ?>
						<?php if ($coordinates !== null): ?>
							<a href="https://mapy.cz/zakladni?q=<?php echo $coordinates; ?>" rel="noopener noreferrer" target="_blank">
								<?php echo $location->getName(); ?>
							</a>
						<?php else: ?>
							<?php echo $location->getName(); ?>
						<?php endif; ?>
					</dd>

					<dt>Kontakt</dt>
					<dd>
						<?php echo $opportunity->getContactPerson()->getName(); ?><br>
						<?php if ($opportunity->getContactPerson()->getPhoneNumber() !== ''): ?>
						<a class="detail__basicInformation-contact" href="tel:<?php echo $opportunity->getContactPerson()->getPhoneNumber(); ?>" rel="noopener noreferrer" target="_blank"><?php echo $opportunity->getContactPerson()->getPhoneNumber(); ?></a><br>
						<?php endif; ?>
						<a class="detail__basicInformation-contact" href="mailto:<?php echo $opportunity->getContactPerson()->getEmailAddress(); ?>" rel="noopener noreferrer" target="_blank"><?php echo $opportunity->getContactPerson()->getEmailAddress(); ?></a>
					</dd>
				</dl>
			</div>

			<section>
				<?php echo $opportunity->getIntroduction(); ?>
			</section>

			<!--section 2-->
			<section>
				<h2>Popis činnosti</h2>
				<?php echo $opportunity->getDescription(); ?>
			</section>

			<!--section 3-->
			<?php if ($opportunity->getLocationBenefits() !== null): ?>
			<section>
				<h2>Přínos pro lokalitu</h2>
				<?php echo $opportunity->getLocationBenefits(); ?>
			</section>
			<?php endif; ?>

			<!--section 4-->
			<section>
				<h2>Přínos ze spolupráce</h2>
				<?php echo $opportunity->getPersonalBenefits(); ?>
			</section>

			<!--section 5-->
			<section>
				<h2>Požadavky</h2>
				<?php echo $opportunity->getRequirements(); ?>
			</section>

			<!--section 6-->
			<section>
				<h2>Komu se ozvat?</h2>
				<address class="hb-fst-n">
					<dl class="prilezitost__contact">
						<dt>Kontaktní osoba:</dt>
						<dd><?php echo $opportunity->getContactPerson()->getName(); ?></dd>
						<dt>E-mail:</dt>
						<dd><a class="prilezitost__email" href="mailto:<?php echo $opportunity->getContactPerson()->getEmailAddress(); ?>" rel="noopener noreferrer" target="_blank"><?php echo $opportunity->getContactPerson()->getEmailAddress(); ?></a></dd>
						<?php if ($opportunity->getContactPerson()->getPhoneNumber() !== ''): ?>
						<dt>Telefon:</dt>
						<dd><a class="prilezitost__phone" href="tel:<?php echo $opportunity->getContactPerson()->getPhoneNumber(); ?>" rel="noopener noreferrer" target="_blank"><?php echo $opportunity->getContactPerson()->getPhoneNumber(); ?></a></dd>
						<?php endif; ?>
					</dl>
				</address>
			</section>
		<?php endif; ?>
	</article>
</main>
