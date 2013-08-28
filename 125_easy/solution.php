<?php

# parse and validate command line arg for file to examine
$options = getopt("f:");

if( ! array_key_exists("f", $options) ) {
    die("Usage: php solution -f input_file\n");
}

if( ! file_exists($options["f"]) ) {
    die("File {$options["f"]} does not exist.\n");
}

$lines = file($options["f"]);

# initialize tracking vars
$word_count = array();
$char_count = array();
$first_count = array();

$total_words = 0;
$total_chars = 0;
$total_symbols = 0;

$lower_char = 'a';
$upper_char = 'A';

# set defaults for a-z,A-Z,0-9
for($i = 0; $i < 26; $i++) {
    $char_count[$lower_char++] = 0;
    $char_count[$upper_char++] = 0;
}
for($i =0; $i < 10; $i++) {
    $char_count[(string) $i] = 0;
}

$first_word = true;

foreach($lines as $line) {

    # if a blank line is given the next word will be the start of a new
    # paragraph
    if(strlen(trim($line)) == 0) {
        $first_word = true;
    }

    # split on whitespace
    $words = preg_split('/\s/', $line, 0, PREG_SPLIT_NO_EMPTY);
    foreach($words as $word) {
        # ignore symbols for word count
        $key = preg_replace('/\W/', '', $word);

        # special tracking for first word in paragraph
        if($first_word) {
            if( ! array_key_exists($key, $first_count) ) {
                $first_count[$key] = 0;
            }
            $first_count[$key]++;
            $first_word = false;
        }

        # init value in word tracker to zero if first time
        if( ! array_key_exists($word, $word_count) ) {
            $word_count[$key] = 0;
        }
        $word_count[$key]++;
        $total_words++;
        # track letters and symbols
        foreach(preg_split('//', $word, 0, PREG_SPLIT_NO_EMPTY) as $letter) {
            if( array_key_exists($letter, $char_count) ) {
                $char_count[$letter]++;
                $total_chars++;
            } else {
                $total_symbols++;
            }
        } 
    }
}

echo "{$total_words} words\n";
echo "{$total_chars} letters\n";
echo "{$total_symbols} symbols\n";

arsort($word_count);
echo "Top three most common words:";
for($i = 0; $i < 3; $i++) {
    echo " " . key($word_count);
    next($word_count);
}
echo "\n";

arsort($char_count);
echo "Top three most common letters:";
for($i = 0; $i < 3; $i++) {
    echo " " . key($char_count);
    next($char_count);
}
echo "\n";

arsort($first_count);
echo key($first_count) . " is the most common first word of all paragraphs\n";

echo "Words only used once:";
foreach($word_count as $word => $count) {
    if($count == 1) {
        echo " " . $word;
    }
}
echo "\n";

echo "Letters not used:";
foreach($char_count as $char => $count) {
    if($count == 0) {
        echo " " . $char;
    }
}
echo "\n";

?>
