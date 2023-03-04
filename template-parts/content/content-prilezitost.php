<?php declare(strict_types = 1);

use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\OpportunityNotFound;
use function HnutiBrontosaurus\Theme\hb_dateSpan;
use function HnutiBrontosaurus\Theme\hb_opportunityCategoryToString;
use const HnutiBrontosaurus\Theme\HB_OPPORTUNITY_ID;


$configuration = hb_getConfiguration();
$bisApiClient = hb_getBisApiClient($configuration);
$dateFormatForHuman = hb_getDateFormatForHuman($configuration);
$dateFormatForRobot = hb_getDateFormatForRobot($configuration);

try {
	$hasBeenUnableToLoad = false;
	$opportunityId = (int) \get_query_var(HB_OPPORTUNITY_ID);
	$opportunity = $bisApiClient->getOpportunity($opportunityId);

} catch (OpportunityNotFound) {
	$hasBeenUnableToLoad = true; // todo change to 404

} catch (ConnectionToBisFailed) {
	$hasBeenUnableToLoad = true;
}
?>

<main role="main">
	<article class="prilezitost">
		<?php if ($hasBeenUnableToLoad): ?>
			<div class="prilezitost__noResults noResults">
				Promiňte, zrovna&nbsp;nám vypadl systém, kde&nbsp;máme uloženy všechny informace o&nbsp;plánovaných akcích.
				Zkuste&nbsp;to prosím za&nbsp;chvilku znovu.
			</div>
		<?php else: ?>
			<h1>
				<span class="prilezitost__heading"><?php echo $opportunity->getName(); ?></span>

				<span class="prilezitost__tags eventTagList">
					<span class="prilezitost__tag eventTagList__item">
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
						<time datetime="<?php echo $opportunity->getStartDate()->toNativeDateTimeImmutable()->format($dateFormatForRobot); ?>">
							<?php echo hb_dateSpan($opportunity->getStartDate(), $opportunity->getEndDate(), $dateFormatForHuman); ?>
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
				<address>
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
