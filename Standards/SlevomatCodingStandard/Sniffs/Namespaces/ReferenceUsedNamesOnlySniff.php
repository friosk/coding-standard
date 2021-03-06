<?php

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer_File;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class ReferenceUsedNamesOnlySniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME = 'ReferenceViaFullyQualifiedName';

	const CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE = 'ReferenceViaFullyQualifiedNameWithoutNamespace';

	const CODE_PARTIAL_USE = 'PartialUse';

	/** @var string[] */
	public $fullyQualifiedKeywords = [];

	/** @var string[] */
	private $normalizedFullyQualifiedKeywords;

	/** @var boolean */
	public $allowFullyQualifiedExceptions = false;

	/** @var string[] */
	public $specialExceptionNames = [];

	/** @var string[]|null */
	private $normalizedSpecialExceptionNames;

	/** @var boolean */
	public $allowPartialUses = true;

	/**
	 * If null, all namespaces are required to be used
	 *
	 * @var string[]|null
	 */
	public $namespacesRequiredToUse;

	/** @var string[]|null */
	private $normalizedNamespacesRequiredToUse;

	/**
	 * @return integer[]
	 */
	public function register()
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @return string[]
	 */
	private function getSpecialExceptionNames()
	{
		if ($this->normalizedSpecialExceptionNames === null) {
			$this->normalizedSpecialExceptionNames = SniffSettingsHelper::normalizeArray($this->specialExceptionNames);
		}

		return $this->normalizedSpecialExceptionNames;
	}

	/**
	 * @return string[]
	 */
	private function getNamespacesRequiredToUse()
	{
		if ($this->namespacesRequiredToUse !== null && $this->normalizedNamespacesRequiredToUse === null) {
			$this->normalizedNamespacesRequiredToUse = SniffSettingsHelper::normalizeArray($this->namespacesRequiredToUse);
		}

		return $this->normalizedNamespacesRequiredToUse;
	}

	/**
	 * @return string[]
	 */
	private function getFullyQualifiedKeywords()
	{
		if ($this->normalizedFullyQualifiedKeywords === null) {
			$this->normalizedFullyQualifiedKeywords = array_map(function ($keyword) {
				if (!defined($keyword)) {
					throw new \SlevomatCodingStandard\Sniffs\Namespaces\UndefinedKeywordTokenException($keyword);
				}
				return constant($keyword);
			}, SniffSettingsHelper::normalizeArray($this->fullyQualifiedKeywords));
		}

		return $this->normalizedFullyQualifiedKeywords;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $openTagPointer
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $openTagPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);
		foreach ($referencedNames as $referencedName) {
			$name = $referencedName->getNameAsReferencedInFile();
			$pointer = $referencedName->getPointer();
			if (NamespaceHelper::isFullyQualifiedName($name)) {
				if (
					(
						!(
						StringHelper::endsWith($name, 'Exception')
						|| in_array(NamespaceHelper::normalizeToCanonicalName($name), $this->getSpecialExceptionNames(), true)
						) || !$this->allowFullyQualifiedExceptions
					) && $this->isClassRequiredToBeUsed($name)
				) {
					$previousKeywordPointer = TokenHelper::findPreviousExcluding($phpcsFile, array_merge(
						TokenHelper::$nameTokenCodes,
						[T_WHITESPACE, T_COMMA]
					), $pointer - 1);
					if (!in_array($tokens[$previousKeywordPointer]['code'], $this->getFullyQualifiedKeywords(), true)) {
						if (!NamespaceHelper::hasNamespace($name) && NamespaceHelper::findCurrentNamespaceName($phpcsFile, $pointer) === null) {
							$phpcsFile->addError(sprintf(
								'Type %s should not be referenced via a fully qualified name, but via an unqualified name without the leading \\, because the file does not have a namespace and the type cannot be put in a use statement',
								$name
							), $pointer, self::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE);
						} else {
							$phpcsFile->addError(sprintf(
								'Type %s should not be referenced via a fully qualified name, but via a use statement',
								$name
							), $pointer, self::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME);
						}
					}
				}
			} elseif (!$this->allowPartialUses) {
				if (NamespaceHelper::isQualifiedName($name)) {
					$phpcsFile->addError(sprintf(
						'Partial use statements are not allowed, but referencing %s found',
						$name
					), $pointer, self::CODE_PARTIAL_USE);
				}
			}
		}
	}

	/**
	 * @param string $name
	 * @return boolean
	 */
	private function isClassRequiredToBeUsed($name)
	{
		if ($this->namespacesRequiredToUse === null) {
			return true;
		}

		foreach ($this->getNamespacesRequiredToUse() as $namespace) {
			if (NamespaceHelper::isTypeInNamespace($name, $namespace)) {
				return true;
			}
		}

		return false;
	}

}
