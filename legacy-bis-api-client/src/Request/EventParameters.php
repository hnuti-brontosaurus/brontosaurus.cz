<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\LegacyBisApiClient\Request;

use HnutiBrontosaurus\LegacyBisApiClient\InvalidArgumentException;


final class EventParameters extends Parameters
{

	const PARAM_DISPLAY_ALREADY_STARTED_KEY = 'probihajici'; // for default just omit this parameter
	const PARAM_DISPLAY_ALREADY_STARTED_VALUE = ''; // whatever value

	const PARAM_ORDER_BY_KEY = 'sort'; // for default sorting just omit this parameter
	const PARAM_ORDER_BY_END_DATE = 'do';


	public function __construct()
	{
		parent::__construct([
			self::PARAM_QUERY => 'akce',
			self::PARAM_DISPLAY_ALREADY_STARTED_KEY => self::PARAM_DISPLAY_ALREADY_STARTED_VALUE,
			self::PARAM_ORDER_BY_KEY => self::PARAM_ORDER_BY_END_DATE,
		]);
	}


	public function setId(int $id): static
	{
		$this->params['id'] = (int) $id;
		return $this;
	}


	// filter

	const FILTER_CLUB = 1;
	const FILTER_WEEKEND = 2;
	const FILTER_CAMP = 4;
	const FILTER_EKOSTAN = 8;

	/**
	 * This parameter serves as combinator for multiple conditions, which can not be achieved with concatenating type, program, target group or any other available parameters.
	 * For example you can not make an union among different parameters. Let's say you want all events which are of type=ohb or of program=brdo. This is not possible with API parameters.
	 * Thus you can take advantage of preset filters which are documented here: https://bis.brontosaurus.cz/myr.php
	 *
	 * Beside standard constant usage as a parameter, you can pass bitwise operation argument, e.g. `EventParameters::FILTER_WEEKEND|EventParameters::FILTER_CAMP`.
	 *
	 * @throws InvalidArgumentException
	 */
	public function setFilter(int $filter): static
	{
		$keys = [
			self::FILTER_CLUB => 'klub',
			self::FILTER_WEEKEND => 'vik',
			self::FILTER_CAMP => 'tabor',
			self::FILTER_EKOSTAN => 'ekostan',
		];

		switch ($filter) {
			case self::FILTER_CLUB:
			case self::FILTER_WEEKEND:
			case self::FILTER_CAMP:
			case self::FILTER_EKOSTAN:
				$param = $keys[$filter];
				break;

			case self::FILTER_WEEKEND | self::FILTER_CAMP:
				$param = $keys[self::FILTER_WEEKEND] . $keys[self::FILTER_CAMP];
				break;

			case self::FILTER_WEEKEND | self::FILTER_EKOSTAN:
				$param = $keys[self::FILTER_WEEKEND] . $keys[self::FILTER_EKOSTAN];
				break;

			default:
				throw new InvalidArgumentException('Value `' . $filter . '` is not of valid types and their combinations for `filter` parameter. Only `weekend+camp` and `weekend+ekostan` can be combined.');
				break;
		}

		$this->params['filter'] = $param;

		return $this;
	}


	// type

	const TYPE_VOLUNTARY = 'pracovni'; // dobrovolnická
	const TYPE_EXPERIENCE = 'prozitkova'; // zážitková
	const TYPE_SPORT = 'sportovni';

	const TYPE_EDUCATIONAL_TALK = 'prednaska'; // vzdělávací - přednášky
	const TYPE_EDUCATIONAL_COURSES = 'vzdelavaci'; // vzdělávací - kurzy, školení
	const TYPE_EDUCATIONAL_OHB = 'ohb'; // vzdělávací - kurz ohb
	const TYPE_LEARNING_PROGRAM = 'vyuka'; // výukový program
	const TYPE_RESIDENTIAL_LEARNING_PROGRAM = 'pobyt'; // pobytový výukový program

	const TYPE_CLUB_MEETUP = 'klub'; // klub - setkání
	const TYPE_CLUB_TALK = 'klub-predn'; // klub - přednáška
	const TYPE_FOR_PUBLIC = 'verejnost'; // akce pro veřejnost
	const TYPE_EKOSTAN = 'ekostan';
	const TYPE_EXHIBITION = 'vystava';
	const TYPE_ACTION_GROUP = 'akcni'; // akční skupina
	const TYPE_INTERNAL = 'jina'; // interní akce (VH a jiné)
	const TYPE_GROUP_MEETING = 'schuzka'; // oddílová, družinová schůzka

	/**
	 * @throws InvalidArgumentException
	 */
	public function setType(string $type): static
	{
		if ( ! \in_array($type, [
			self::TYPE_VOLUNTARY,
			self::TYPE_EXPERIENCE,
			self::TYPE_SPORT,

			self::TYPE_EDUCATIONAL_TALK,
			self::TYPE_EDUCATIONAL_COURSES,
			self::TYPE_EDUCATIONAL_OHB,
			self::TYPE_LEARNING_PROGRAM,
			self::TYPE_RESIDENTIAL_LEARNING_PROGRAM,

			self::TYPE_CLUB_MEETUP,
			self::TYPE_CLUB_TALK,
			self::TYPE_FOR_PUBLIC,
			self::TYPE_EKOSTAN,
			self::TYPE_EXHIBITION,
			self::TYPE_ACTION_GROUP,
			self::TYPE_INTERNAL,
			self::TYPE_GROUP_MEETING,
		], true)) {
			throw new InvalidArgumentException('Value `' . $type . '` is not of valid types for `type` parameter.');
		}

		$this->params['typ'][] = $type;
		return $this;
	}

	/**
	 * @param string[] $types
	 */
	public function setTypes(array $types): static
	{
		foreach ($types as $type) {
			$this->setType($type);
		}

		return $this;
	}


	// program

	const PROGRAM_NOT_SELECTED = 'none';
	const PROGRAM_NATURE = 'ap';
	const PROGRAM_SIGHTS = 'pamatky';
	const PROGRAM_BRDO = 'brdo';
	const PROGRAM_EKOSTAN = 'ekostan';
	const PROGRAM_PSB = 'psb';
	const PROGRAM_EDUCATION = 'vzdelavani';

	/**
	 * @throws InvalidArgumentException
	 */
	public function setProgram(string $program): static
	{
		if ( ! \in_array($program, [
			self::PROGRAM_NOT_SELECTED,
			self::PROGRAM_NATURE,
			self::PROGRAM_SIGHTS,
			self::PROGRAM_BRDO,
			self::PROGRAM_EKOSTAN,
			self::PROGRAM_PSB,
			self::PROGRAM_EDUCATION,
		], true)) {
			throw new InvalidArgumentException('Value `' . $program . '` is not of valid types for `program` parameter.');
		}

		$this->params['program'][] = $program;
		return $this;
	}

	/**
	 * @param string[] $programs
	 */
	public function setPrograms(array $programs): static
	{
		foreach ($programs as $program) {
			$this->setProgram($program);
		}

		return $this;
	}


	// target group

	const TARGET_GROUP_EVERYONE = 'vsichni';
	const TARGET_GROUP_ADULTS = 'dospeli';
	const TARGET_GROUP_CHILDREN = 'deti';
	const TARGET_GROUP_FAMILIES = 'detirodice';
	const TARGET_GROUP_FIRST_TIME_ATTENDEES = 'prvouc';

	/**
	 * @throws InvalidArgumentException
	 */
	public function setTargetGroup(string $targetGroup): static
	{
		if ( ! \in_array($targetGroup, [
			self::TARGET_GROUP_EVERYONE,
			self::TARGET_GROUP_ADULTS,
			self::TARGET_GROUP_CHILDREN,
			self::TARGET_GROUP_FAMILIES,
			self::TARGET_GROUP_FIRST_TIME_ATTENDEES,
		], true)) {
			throw new InvalidArgumentException('Value `' . $targetGroup . '` is not of valid types for `for` parameter.');
		}

		$this->params['pro'][] = $targetGroup;
		return $this;
	}

	/**
	 * @param string[] $targetGroups
	 */
	public function setTargetGroups(array $targetGroups): static
	{
		foreach ($targetGroups as $targetGroup) {
			$this->setTargetGroup($targetGroup);
		}

		return $this;
	}


	// date constraints

	const PARAM_DATE_FORMAT = 'Y-m-d';

	public function setFrom(\DateTimeImmutable $dateFrom): static
	{
		$this->params['od'] = $dateFrom->format(self::PARAM_DATE_FORMAT);
		return $this;
	}

	public function setUntil(\DateTimeImmutable $dateFrom): static
	{
		$this->params['do'] = $dateFrom->format(self::PARAM_DATE_FORMAT);
		return $this;
	}

	public function setYear(int $year): static
	{
		$this->params['rok'] = (int) $year;
		return $this;
	}

	public function hideTheseAlreadyStarted(): static
	{
		unset($this->params[self::PARAM_DISPLAY_ALREADY_STARTED_KEY]);
		return $this;
	}


	// miscellaneous

	public function orderByStartDate(): static
	{
		unset($this->params[self::PARAM_ORDER_BY_KEY]);
		return $this;
	}

	/**
	 * @param int|int[] $unitIds
	 */
	public function setOrganizedBy(array|int $unitIds): static
	{
		$organizedByKey = 'zc';

		// If just single value, wrap it into an array.
		if ( ! \is_array($unitIds)) {
			$unitIds = [$unitIds];
		}

		foreach ($unitIds as $unitId) {
			// If such value is not present yet, initialize it with an empty array.
			if ( ! \is_array($this->params[$organizedByKey])) {
				$this->params[$organizedByKey] = [];
			}

			$this->params[$organizedByKey][] = (int) $unitId;
		}

		return $this;
	}

	public function includeNonPublic(): static
	{
		$this->params['vse'] = 1;
		return $this;
	}

}
