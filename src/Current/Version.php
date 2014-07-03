<?php

namespace Current;

class Version
{
    protected $major;
    protected $minor;
    protected $patch;

    protected $prerelease = array();

    protected $build;

    protected $stable = true;

    public function __construct($version, $markedUnstable = null)
    {
        $version = trim($version);

        // remove first v, if it's there
        if (substr($version, 0, 1) == 'v') {
            $version = substr($version, 1);
        }

        // split off 'build'
        if (($pos = strpos($version, '+')) !== false) {
            $this->build = substr($version, $pos +1);
            $version = substr($version, 0, $pos);
        }

        // split off prerelease data
        if (($pos = strpos($version, '-')) !== false) {
            $versionPrerelease = substr($version, $pos +1);
            $version = substr($version, 0, $pos);
            $this->stable = false;
            $this->prerelease = explode('.', $versionPrerelease);
        }

        // Override defaults and automatic stability setting with user choice
        if (isset($markedUnstable) && !is_null($markedUnstable)) {
            $this->stable = !$markedUnstable;
        }

        // Finally fill in the standard version pieces.
        $versionPieces = explode('.', $version);
        $this->major = $versionPieces[0];
        $this->minor = isset($versionPieces[1]) ? $versionPieces[1] : 0;
        $this->patch = isset($versionPieces[2]) ? $versionPieces[2] : 0;
    }

    public function getMajor()
    {
        return $this->major;
    }

    public function getMinor()
    {
        return $this->minor;
    }

    public function getPatch()
    {
        return $this->patch;
    }

    public function getPrerelease($offset = 0)
    {
        if (!isset($this->prerelease[$offset])) {
            return false;
        }

        return $this->prerelease[$offset];
    }

    public static function compare($a, $b)
    {
        // $a > $b = 1
        // $a == $b = 0
        // $a < $b = -1

        if (!($a instanceof Version) || is_string($a)) {
            $a = new Version($a);
        }

        if (!($b instanceof Version) || is_string($b)) {
            $b = new Version($b);
        }

        if (($amajor = $a->getMajor()) !== $b->getMajor()) {
            return $a->getMajor() > $b->getMajor() ? 1 : -1;
        }

        if ($a->getMinor() !== $b->getMinor()) {
            return $a->getMinor() > $b->getMinor() ? 1 : -1;
        }

        if ($a->getPatch() !== $b->getPatch()) {
            return $a->getPatch() > $b->getPatch() ? 1 : -1;
        }

        $i = 0;
        do{
            $metaA = $a->getPrerelease($i);
            $metaB = $b->getPrerelease($i);

            if (($metaA && $metaB) === false) {

                // At least one of the two values is false.

                if ($metaA == $metaB) {
                    // Both False, meaning there are no more parts and previous ones were all equal.
                    return 0;

                } elseif ($metaA === false) {
                    return ($i === 0)
                        ? 1 // If A is a full release and B is a pre-release then A has precedence.
                        : -1; // If A and B are both pre-release and A has fewer tags then B takes precedence.

                } elseif ($metaB ===false) {
                    return ($i === 0)
                        ? -1 // If B is a full release and A is a pre-release then B has precedence.
                        : 1; // If A and B are both pre-release and B has fewer tags then A takes precedence.
                }
            }

            // Both are equal and neither are false, so we move on to the next piece.

            if ($metaA == $metaB) {
                continue;
            }

            if (!is_numeric($metaA) || !is_numeric($metaB)) {

                // At least one value is a string instead of a number.

                if (!is_numeric($metaA) && !is_numeric($metaB)) {

                    // If both are strings do a comparison. The nature sort algorithm is used so alpha1 < alpha10.
                    return strnatcmp($metaA, $metaB);

                } elseif (!is_numeric($metaA)) {
                    // a uses a string while b uses a number, so a has higher precedence.
                    return 1;

                } elseif (!is_numeric($metaB)) {
                    // b uses a string while a uses a number, so a has higher precedence.
                    return -1;
                }

            } else {
                // Both are numbers but they are not equal, so we do a direct compare.
                return $metaA > $metaB ? 1 : -1;
            }

            // This side never gets reached.

        }while (++$i);
    }

    public function getLongString()
    {
        $version = 'v' . $this->getMajor() . '.' . $this->getMinor() . '.' . $this->getPatch();

        if (count($this->prerelease)) {
            $version .= '-' . implode('.', $this->prerelease);
        }

        if (isset($this->build)) {
            $version .= '+' . $this->build;
        }

        return $version;
    }

    public function getShortString()
    {
        $version = 'v' . $this->getMajor();

        $minor = $this->getMinor();
        $patch = $this->getPatch();

        if (($minor != 0) || ($patch != 0)) {
            $version .= '.' . $minor;
        }

        if ($patch != 0) {
            $version .= '.' . $patch;
        }

        if (count($this->prerelease) > 0) {
            $version .= '-' . implode('.', $this->prerelease);
        }

        return $version;
    }

    public function __toString()
    {
        return $this->getLongString();
    }

}
