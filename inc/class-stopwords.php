<?php 

/**
 * Stop Words class
 * 
 * @param array $basic_words    simple stop words list
 * @param array $strict_words   strict stop words list
 */
class QMeanStopWords
{
    public static $basic_words = array(
        "the","and","or","for","but","yet","so","lot","of","all","a",
        "his","her","he","she","that", "any","an","in","with","to"
    );

    public static $strict_words = array(
        "a","ii","about","above","according","across","actually","ad",
        "adj","ae","af","after","afterwards","ag","again","ai","al",
        "all","almost","alone","along","already","also","although","always",
        "am","among","amongst","an","and","another","any","anyhow","anyone",
        "anything","anywhere","ao","aq","ar","are","aren","aren't","around",
        "arpa","as","at","au","aw","az","b","ba","bb","bd","be","became",
        "because","become","becomes","becoming","been","before","beforehand",
        "beginning","behind","being","below","beside","besides",
        "between","bf","bg","bh","bi","bj","bm","bn","bo",
        "both","br","bs","bt","but","buy","bv","bw","by","bz","c","ca","can",
        "can't","cannot","cc","cd","cf","cg","ch","ci","ck","cl",
        "click","cm","cn","co",
        "co.","com","copy","could","couldn","couldn't","cr","cs","cu","cv","cx",
        "cy","cz","d","de","did","didn","didn't","dj","dk","dm","do","does",
        "doesn","doesn't","don","don't","down","during","dz","e","each","ec",
        "edu","ee","eg","eh","eight","eighty","either","else","elsewhere","end",
        "ending","enough","er","es","et","etc","even","ever","every",
        "everywhere","except","f","few","fi","find",
        "first","five","fj","fk","fm","fo","for","former","formerly","forty",
        "found","four","fr","from","further","fx","g","ga","gb","gd",
        "ge","get","gf","gg","gh","gi","gl","gm","gmt","gn","go","gov","gp",
        "gq","gr","gs","gt","gu","gw","gy","h","had","has","hasn","hasn't",
        "have","haven","haven't","he","he'd","he'll","he's","help","hence",
        "her","here","here's","hereafter","hereby","herein","hereupon","hers",
        "herself","him","himself","his","hk","hm","hn",
        "however","hr","ht","htm","html","http","hu","i","i'd","i'll",
        "i'm","i've","i.e.","id","ie","if","il","im","in","inc",
        "inc.","indeed","instead","int","into","io","iq","ir","is",
        "isn","isn't","it","it's","its","itself","j","je","jm","jo","jp",
        "k","ke","kg","kh","ki","km","kn","kp","kr","kw","ky","kz","l","la",
        "last","later","latter","lb","lc","least","less","let","let's","li",
        "like","likely","lk","ll","lr","ls","lt","ltd","lu","lv","ly","m","ma",
        "many","maybe","mc","md","me","meantime",
        "meanwhile","mg","mh","might","mil","miss","mk",
        "ml","mm","mn","mo","more","moreover","mostly","mp","mq","mr",
        "mrs","ms","msie","mt","mu","much","must","mv","mw","mx","my","myself",
        "mz","n","na","namely","nc","ne","neither","net","never",
        "nevertheless","nf","ng","ni","nl","no",
        "none","nonetheless","noone","nor","not","nothing","now",
        "nowhere","np","nr","nu","nz","o","of","off","often","om","on","once",
        "one","one's","only","onto","or","org","other","others","otherwise",
        "our","ours","ourselves","out","over","overall","own","p","pa",
        "pe","per","perhaps","pf","pg","ph","pk","pl","pm","pn","pr","pt","pw",
        "py","q","qa","r","rather","re","recent","recently",
        "ro","ru","rw","s","sa","same","sb","sc","sd","se","seem","seemed",
        "seeming","seems","several","sg","sh","she","she'd",
        "she'll","she's","should","shouldn","shouldn't","si","since",
        "six","sixty","sj","sk","sl","sm","sn","so","some","somehow","someone",
        "something","sometime","sometimes","somewhere","sr","st","still",
        "su","such","sv","sy","sz","t","taking","tc","td","ten","tf",
        "tg","th","than","that","that'll","that's","the","their","them",
        "themselves","then","thence","there","there'll","there's","thereafter",
        "thereby","therefore","therein","thereupon","these","they","they'd",
        "they'll","they're","they've","this","those","though",
        "thousand","through","throughout","thru","thus","tj","tk","tm",
        "tn","to","together","too","toward","towards","tp","tr","tt",
        "tv","tw","twenty","two","tz","u","ua","ug","uk","um","under","unless",
        "unlike","unlikely","until","up","upon","us","use","used","using","uy",
        "uz","v","va","vc","ve","very","vg","vi","via","vn","vu","w","was",
        "wasn","wasn't","we","we'd","we'll","we're","we've",
        "well","were","weren","weren't","wf",
        "what'll","what's","whatever","whence","whenever","where",
        "whereafter","whereas","whereby","wherein","whereupon","wherever",
        "whether","while","whither","who","who'd","who'll","who's",
        "whoever","NULL","whole","whom","whomever","whose","will","with",
        "within","without","won","won't","would","wouldn","wouldn't","ws","www",
        "x","y","ye","yes","yet","you","you'd","you'll","you're","you've",
        "your","yours","yourself","yourselves","yt","yu","z","za","zm","zr",
        "10","z","org","inc"
    );

    /**
     * Retrun stop word list
     * 
     * @param  boolean $stric    if true uses stric_words property
     * @return array             stop words list
     */
    public static function get_default($strict = false)
    {
        return $strict ? static::$strict_words : static::$basic_words;
    }
}