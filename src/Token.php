<?php
/*---------------------------------------------------------------------------------------------
 * Copyright (c) Microsoft Corporation. All rights reserved.
 *  Licensed under the MIT License. See License.txt in the project root for license information.
 *--------------------------------------------------------------------------------------------*/

namespace PhpParser;

class Token implements \JsonSerializable {
    // TODO optimize memory - ideally this would be a struct of 4 ints
    public $kind;
    public $fullStart;
    public $start;
    public $length;

    public function __construct($kind, $fullStart, $start, $length) {
        $this->kind = $kind;
        $this->fullStart = $fullStart;
        $this->start = $start;
        $this->length = $length;
    }

    public function getTriviaForToken(string $document) : string {
        return substr($document, $this->fullStart, $this->start - $this->fullStart);
    }

    public function getTextForToken(string $document) : string {
        return substr($document, $this->start, $this->length - ($this->start - $this->fullStart));
    }

    public function getFullTextForToken(string $document) : string {
        return substr($document, $this->fullStart, $this->length);
    }

    public function getEnd() {
        return $this->fullStart + $this->length;
    }

    function jsonSerialize() {
        $kindName = $this->getTokenKindNameFromValue($this->kind);

        if (!isset($GLOBALS["SHORT_TOKEN_SERIALIZE"])) {
            $GLOBALS["SHORT_TOKEN_SERIALIZE"] = false;
        }
        
        if ($GLOBALS["SHORT_TOKEN_SERIALIZE"]) {
            return [
                "kind" => $kindName,
                "textLength" => $this->length - ($this->start - $this->fullStart)
            ];
        } else {
            return [
                "kind" => $kindName,
                "fullStart" => $this->fullStart,
                "start" => $this->start,
                "length" => $this->length
            ];
        }        
    }

    public static function getTokenKindNameFromValue($kindName) {
        $constants = (new \ReflectionClass("PhpParser\\TokenKind"))->getConstants();
        foreach ($constants as $name => $val) {
            if ($val == $kindName) {
                $kindName = $name;
            }
        }
        return $kindName;
    }
}