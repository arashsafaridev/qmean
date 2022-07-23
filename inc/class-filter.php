<?php

/**
 * Filter the text out of stop words
 */
class QMeanFilter
{
    /**
     * List of stop words
     *
     * @var array
     */
    protected $stop_words = [];


    /**
     * @param optional words to set
     */
    public function __construct($words = null)
    {
        if ($words) {
            if (!is_array($words)) {
                $words = [$words];
            }
            $this->set_words($words);
        } else {
            $words = QMeanStopWords::get_default();
            $this->set_words($words);
        }
    }

    /**
     * Clean text using stop words
     *
     * @param  string $text   text to clean
     *
     * @return string cleaned text
     */
    public function clean_text($text = '')
    {
        $text = array_udiff(explode(' ', $text), $this->stop_words, 'strcasecmp');
        return implode(' ', $text);
    }

    /**
     * Get array of stop words
     *
     * @return array Returns array of stop words
     */
    public function get_words()
    {
        return $this->stop_words;
    }

    /**
     * Set array of stop words
     *
     * @param array list of stop words to set
     *
     * @return QMeanStopWords object
     */
    public function set_words(array $words = [])
    {
        $this->stop_words = $words;
        return $this;
    }

    /**
     * Merge array of stop words
     *
     * @param array list of stop words to merge
     *
     * @return QMeanStopWords object
     */
    public function merge_words(array $words = [])
    {
        $this->stop_words = array_unique(
            array_merge($this->stop_words, $words)
        );
        return $this;
    }
}