<?php declare(strict_types=1);

/**
 * Copyright israelik (C)
 * This file is Written by israelik for israelisBots!
 *
 * @author    israelik 
 * @copyright israelik
 * @license   https://opensource.org/license/mit MIT License
 * @link israelik => https://t.me/israelik | israelisBots => https://t.me/israelisBots
 */
  
use danog\MadelineProto\Broadcast\Filter;
use danog\MadelineProto\Broadcast\Progress;
use danog\MadelineProto\Broadcast\Status;
use danog\MadelineProto\EventHandler\Attributes\Cron;
use danog\MadelineProto\EventHandler\Attributes\Handler;
use danog\MadelineProto\EventHandler\Filter\FilterCommandCaseInsensitive;
use danog\MadelineProto\EventHandler\Message;
use danog\MadelineProto\EventHandler\Message\ChannelMessage;
use danog\MadelineProto\EventHandler\Message\PrivateMessage;
use danog\MadelineProto\EventHandler\Message\GroupMessage;
use danog\MadelineProto\EventHandler\SimpleFilter\FromAdmin;
use danog\MadelineProto\EventHandler\SimpleFilter\Incoming;
use danog\MadelineProto\ParseMode;
use danog\MadelineProto\Settings;
use danog\MadelineProto\SimpleEventHandler;
use danog\MadelineProto\BotApiFileId;
use danog\MadelineProto\EventHandler\CallbackQuery;
use danog\MadelineProto\EventHandler\Filter\FilterButtonQueryData;
use danog\MadelineProto\EventHandler\Filter\Combinator\FiltersOr;
use danog\MadelineProto\EventHandler\Filter\FilterIncoming;
use danog\MadelineProto\EventHandler\Update;
use Amp\File;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;

if (class_exists(API::class)) {
} elseif (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
} else {
    if (!file_exists('madeline.php')) {
        copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
    }
    require_once 'madeline.php';
}

class MyEventHandler extends SimpleEventHandler
{

    public const CLOSER = "הקבוצה שלנו שומרת שבת ותהיה סגורה עד צאת השבת 🕯
🇮🇱 שבת שלום לכולם 🇮🇱"; 

    public const OPENER = "🇮🇱 שבוע טוב לכולם! 🇮🇱
הקבוצה פתוחה לכתיבת הודעות.";	

    private array $notifiedChats = [];

    public function __sleep(): array
    {
        return ['notifiedChats'];
    }

    public function getReportPeers()
    {
$ADMIN = parse_ini_file('.env')['ADMIN'];
$ADMIN = array_map('trim', explode(',', $ADMIN));
        return $ADMIN;
    }

 #[FilterIncoming]
    public function ChannelsLeave(ChannelMessage $message): void 
    {
try {
$this->channels->leaveChannel(channel: $message->chatId );
} catch (Throwable $e) {
}
}

#[FilterCommandCaseInsensitive('start')]
    public function StartCommand(Incoming & PrivateMessage  $message): void
    {
try {
$senderid = $message->senderId;
$messageid = $message->id;
$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$me = $this->getSelf();
$me_username = $me['username'];

$txtbot = "היי <a href='mention:$senderid'>$first_name</a>, ברוך הבא 👋
הרובוט שישמור את השבת בקבוצה שלך!🕯";

$bot_API_markup[] = [['text'=>"זמני כניסת השבת 🕯",'callback_data'=>"זמנישבת"]];
$bot_API_markup[] = [['text'=>"הוסף אותי לקבוצה ➕",'url'=>"https://t.me/$me_username?startgroup&admin=restrict_members"]];
$bot_API_markup[] = [['text'=>"📖 כל הפקודות 💡",'callback_data'=>"כלהפקודות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];


$inputReplyToMessage = ['_' => 'inputReplyToMessage', 'reply_to_msg_id' => $messageid];
$this->messages->sendMessage(no_webpage: true, peer: $message->senderId, reply_to: $inputReplyToMessage, message: "$txtbot", reply_markup: $bot_API_markup, parse_mode: 'HTML');

    if (!file_exists(__DIR__."/data")) {
mkdir(__DIR__."/data");
}
    if (!file_exists(__DIR__."/data/$senderid")) {
mkdir(__DIR__."/data/$senderid");
}
    if (file_exists(__DIR__."/data/$senderid/grs1.txt")) {
unlink(__DIR__."/data/$senderid/grs1.txt");
}
} catch (Throwable $e) {
}
}

#[FilterButtonQueryData('חזרה')]
public function BackCommand(callbackQuery $query)
{
try {
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$me = $this->getSelf();
$me_username = $me['username'];

$txtbot = "היי <a href='mention:$userid'>$first_name</a>, ברוך הבא 👋
הרובוט שישמור את השבת בקבוצה שלך!🕯";

$bot_API_markup[] = [['text'=>"זמני כניסת השבת 🕯",'callback_data'=>"זמנישבת"]];
$bot_API_markup[] = [['text'=>"הוסף אותי לקבוצה ➕",'url'=>"https://t.me/$me_username?startgroup&admin=restrict_members"]];
$bot_API_markup[] = [['text'=>"📖 כל הפקודות 💡",'callback_data'=>"כלהפקודות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "$txtbot", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = true, $scheduleDate = NULL);
    if (file_exists(__DIR__."/"."data/$userid/grs1.txt")) {
unlink(__DIR__."/"."data/$userid/grs1.txt");
}
} catch (Throwable $e) {
}
}
	
#[FilterButtonQueryData('זמנישבת')]
public function ShabbatTimes(callbackQuery $query)
{	
try {
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$bot_API_markup2[] = [['text'=>"⌛️",'callback_data'=>"⌛️"]];
$bot_API_markup2 = [ 'inline_keyboard'=> $bot_API_markup2,];

$editer = $query->editText($message = "<b>בודק זמנים... אנא המתן</b> ⌛️", $replyMarkup = $bot_API_markup2, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

$client1 = HttpClientBuilder::buildDefault();
$response1 = $client1->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=281184&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body1 = $response1->getBody()->buffer();
$clean_text1 = strip_tags($body1); 
$lines1 = explode("\n", $clean_text1);
foreach ($lines1 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result11 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result21 = $line;
    }
    if (strpos($line, "פרשת") !== false) {
$result31 = $line;
}
}
$result11 = trim($result11);
$result21 = trim($result21);
$result31 = trim($result31);
preg_match('/\d{2}:\d{2}/', $result11, $matches);
$timein = $matches[0];
preg_match('/\d{2}:\d{2}/', $result21, $matches);
$timeout = $matches[0];
if($result31 != null){
$updatedLine3 = str_ireplace("this week’s Torah portion is", "", $result31);
if (preg_match('/^\s/', $updatedLine3)) {
$updatedLine3 = ltrim($updatedLine3, ' ');
}
$updatedLine3 = rtrim($updatedLine3);
$updatedLine3 = "$updatedLine3 | ";
}
if($result31 == null){
$updatedLine3 = null;
}
$resultline4 = strstr($result21, ',');
$resultline4 = str_ireplace(",", "", $resultline4);
if (preg_match('/^\s/', $resultline4)) {
$resultline4 = ltrim($resultline4, ' ');
}
$resultline4 = rtrim($resultline4);

$client2 = HttpClientBuilder::buildDefault();
$response2 = $client2->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=294801&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body2 = $response2->getBody()->buffer();
$clean_text2 = strip_tags($body2); 
$lines2 = explode("\n", $clean_text2);
foreach ($lines2 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result12 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result22 = $line;
    }
}
$result12 = trim($result12);
$result22 = trim($result22);
preg_match('/\d{2}:\d{2}/', $result12, $matches);
$timeinzman2 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result22, $matches);
$timeoutzman2 = $matches[0];

$client3 = HttpClientBuilder::buildDefault();
$response3 = $client3->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=293397&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body3 = $response3->getBody()->buffer();
$clean_text3 = strip_tags($body3); 
$lines3 = explode("\n", $clean_text3);
foreach ($lines3 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result13 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result23 = $line;
    }
}
$result13 = trim($result13);
$result23 = trim($result23);
preg_match('/\d{2}:\d{2}/', $result13, $matches);
$timeinzman3 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result23, $matches);
$timeoutzman3 = $matches[0];

$client4 = HttpClientBuilder::buildDefault();
$response4 = $client4->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=295530&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body4 = $response4->getBody()->buffer();
$clean_text4 = strip_tags($body4); 
$lines4 = explode("\n", $clean_text4);
foreach ($lines4 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result14 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result24 = $line;
    }
}
$result14 = trim($result14);
$result24 = trim($result24);
preg_match('/\d{2}:\d{2}/', $result14, $matches);
$timeinzman4 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result24, $matches);
$timeoutzman4 = $matches[0];

$numdate1 = "ינואר";
$numdate2 = "פברואר";
$numdate3 = "מרץ";
$numdate4 = "אפריל";
$numdate5 = "מאי";
$numdate6 = "יוני";
$numdate7 = "יולי";
$numdate8 = "אוגוסט";
$numdate9 = "ספטמבר";
$numdate10 = "אוקטובר";
$numdate11 = "נובמבר";
$numdate12 = "דצמבר";

$stringres4 = "$resultline4";
$resultres4 = preg_replace('/\d/', '', $stringres4);
$resultline4new = rtrim($resultres4);
$resultres44 = str_replace(' ', '', $resultline4new);

if($resultres44 == $numdate1){
$hodesh = "01";
}	
if($resultres44 == $numdate2){
$hodesh = "02";
}	
if($resultres44 == $numdate3){
$hodesh = "03";
}	
if($resultres44 == $numdate4){
$hodesh = "04";
}	
if($resultres44 == $numdate5){
$hodesh = "05";
}	
if($resultres44 == $numdate6){
$hodesh = "06";
}	
if($resultres44 == $numdate7){
$hodesh = "07";
}	
if($resultres44 == $numdate8){
$hodesh = "08";
}	
if($resultres44 == $numdate9){
$hodesh = "09";
}	
if($resultres44 == $numdate10){
$hodesh = "10";
}	
if($resultres44 == $numdate11){
$hodesh = "11";
}	
if($resultres44 == $numdate12){
$hodesh = "12";
}	

preg_match_all('!\d+!', $resultline4, $matches);
$numbers = implode(' ', $matches[0]);
$numbersout = $numbers; 
$messageLength = mb_strlen((string)$numbersout);
if($messageLength == 1){
$numbersout = "0".$numbersout;
}
$lastTwoDigits = date('Y');
$dateshabatout = "$numbersout/$hodesh/$lastTwoDigits";

$zmanim = "⌚️ <u><b>זמני כניסת ויציאת השבת:</b></u>

🗓 $updatedLine3$dateshabatout

🕯 <u>זמני כניסת השבת:</u>
ירושלים: <code>$timein</code>
חיפה: <code>$timeinzman2</code>
תל אביב: <code>$timeinzman3</code>
באר שבע: <code>$timeinzman4</code>

🍷 <u>זמני יציאת השבת:</u>
ירושלים: <code>$timeout</code>
חיפה: <code>$timeoutzman2</code>
תל אביב: <code>$timeoutzman3</code>
באר שבע: <code>$timeoutzman4</code>"; 

$editer2 = $query->editText($message = "$zmanim", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
} catch (Throwable $e) {
$this->messages->sendMessage(peer: $query->userId, message: $e->getMessage());
}
}

#[FilterButtonQueryData('כלהפקודות')]
public function AllCommands(callbackQuery $query)
{
try {
$txtbot = "<b>ברוכים הבאים לתפריט העזרה!</b> 🆘
בתפריט זה תמצאו את כל הפקודות והמידע";

$bot_API_markup[] = [['text'=>"כללי יסוד",'callback_data'=>"כללייסוד"]];
$bot_API_markup[] = [['text'=>"פקודות למנהלים",'callback_data'=>"פקודותלמנהלים"]];
$bot_API_markup[] = [['text'=>"פקודות לכל המשתמשים",'callback_data'=>"פקודותלכלהמשתמשים"]];
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "$txtbot", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
} catch (Throwable $e) {
}
}

#[FilterButtonQueryData('כללייסוד')]
public function Rules(callbackQuery $query)
{
try {
$txtbot = "<b>(הרובוט הזה עובד רק בסופר קבוצה)</b>

🕯 על מנת שאני יוכל לסגור את הקבוצה בשבת, יש להוסיף אותי לקבוצה שלך כמנהל עם הרשאה לחסימת משתמשים.

לאחר ההוספה חובה לשלוח בקבוצה את הפקודה <code>/add</code> אחרת אני לא אשמור את השבת אצלך בקבוצה...

אתה יכול להשתמש בסימנים: /, !, . כדי להפעיל כל פקודה.

זכור: אתה צריך להשתמש בפקודות בתוך הקבוצה, אלא אם כן הם תוכננו במיוחד עבור כל צ'אט (ראה 'פקודות לכל המשתמשים').";

$me = $this->getSelf();
$me_username = $me['username'];

$bot_API_markup[] = [['text'=>"הוסף אותי לקבוצה ➕",'url'=>"https://t.me/$me_username?startgroup&admin=restrict_members"]];
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"כלהפקודות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "$txtbot", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
} catch (Throwable $e) {
}
}

#[FilterButtonQueryData('פקודותלמנהלים')]
public function CommandForAdmins(callbackQuery $query)
{
try {
$txtbot = "💡 <b>רשימת פקודות זמינות:</b>
/add - שליחת פקודה זו בקבוצה תוסיף את הקבוצה לבסיס נתונים על מנת שהיא תסגר בשבת!
/remove - הסרת הקבוצה מהבסיס נתונים... הקבוצה לא תסגר בשבת!
/settings - התאם אישית את הרובוט בקבוצה שלך. 

⚙️ <b>מה אפשר לעשות בהגדרות?</b>
באפשרותכם להגדיר האם הקבוצה תקבל מידי יום שישי (בשעה 13:30) הודעה עם זמני כניסת השבת!
כמו כן באפשרותכם להגדיר הודעה מותאמת אישית שתשלח בערב שבת כשהקבוצה נסגרת!
והודעה מותאמת אישית שתשלח במוצאי שבת כשהקבוצה נפתחת!

<b>*בקרוב תמיכה במדיה וכפתורים מוטבעים להודעה מותאמת אישית.</b>

<b>הקבוצה תיסגר לפי זמן:</b> ירושלים 
(10 דק' לפני כניסת השבת.)
<b>בקרוב --></b> בחירת איזור זמן ⌚️

<i>פקודות אלו יש לשלוח בקבוצה בלבד</i>";

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"כלהפקודות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "$txtbot", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
} catch (Throwable $e) {
}
}

#[FilterButtonQueryData('פקודותלכלהמשתמשים')]
public function CommandForAll(callbackQuery $query)
{
try {
$me = $this->getSelf();
$me_username = '@'.$me['username'];

$txtbot = "💡 <b>רשימת פקודות זמינות:</b>
/shabat - הצגת זמני כניסת ויציאת השבת.
/stats - כמה קבוצות שומרות שבת 📊
/donate - תמיכה ברובוט ⭐️

<b>ניתן גם להשתמש במצב אינליין:</b>
<code>$me_username shabat</code>

<i>פקודות אלו ניתן לשלוח בכל צ'אט</i>";

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"כלהפקודות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "$txtbot", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
} catch (Throwable $e) {
}
}

    #[FilterCommandCaseInsensitive('shabat')]
    public function shabatCommand(Incoming $message): void
    {

$senderid = $message->senderId;
$messageid = $message->id;
$chatid = $message->chatId;

$inputReplyToMessage = ['_' => 'inputReplyToMessage', 'reply_to_msg_id' => $messageid];
$sentMessage = $this->messages->sendMessage(peer: $chatid, reply_to: $inputReplyToMessage, message: "<b>בודק זמנים... אנא המתן</b> ⌛️", parse_mode: 'HTML');
$sentMessage2 = $this->extractMessageId($sentMessage);

try {
$client1 = HttpClientBuilder::buildDefault();
$response1 = $client1->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=281184&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body1 = $response1->getBody()->buffer();
$clean_text1 = strip_tags($body1); 
$lines1 = explode("\n", $clean_text1);
foreach ($lines1 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result11 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result21 = $line;
    }
    if (strpos($line, "פרשת") !== false) {
$result31 = $line;
}
}

$result11 = trim($result11);
$result21 = trim($result21);
$result31 = trim($result31);

preg_match('/\d{2}:\d{2}/', $result11, $matches);
$timein = $matches[0];
preg_match('/\d{2}:\d{2}/', $result21, $matches);
$timeout = $matches[0];

if($result31 != null){
$updatedLine3 = str_ireplace("this week’s Torah portion is", "", $result31);
if (preg_match('/^\s/', $updatedLine3)) {
$updatedLine3 = ltrim($updatedLine3, ' ');
}
$updatedLine3 = rtrim($updatedLine3);
$updatedLine3 = "$updatedLine3 | ";
}
if($result31 == null){
$updatedLine3 = null;
}

$resultline4 = strstr($result21, ',');
$resultline4 = str_ireplace(",", "", $resultline4);

if (preg_match('/^\s/', $resultline4)) {
$resultline4 = ltrim($resultline4, ' ');
}
$resultline4 = rtrim($resultline4);


$client2 = HttpClientBuilder::buildDefault();
$response2 = $client2->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=294801&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body2 = $response2->getBody()->buffer();
$clean_text2 = strip_tags($body2); 
$lines2 = explode("\n", $clean_text2);
foreach ($lines2 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result12 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result22 = $line;
    }
}
$result12 = trim($result12);
$result22 = trim($result22);
preg_match('/\d{2}:\d{2}/', $result12, $matches);
$timeinzman2 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result22, $matches);
$timeoutzman2 = $matches[0];

$client3 = HttpClientBuilder::buildDefault();
$response3 = $client3->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=293397&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body3 = $response3->getBody()->buffer();
$clean_text3 = strip_tags($body3); 
$lines3 = explode("\n", $clean_text3);
foreach ($lines3 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result13 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result23 = $line;
    }
}
$result13 = trim($result13);
$result23 = trim($result23);
preg_match('/\d{2}:\d{2}/', $result13, $matches);
$timeinzman3 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result23, $matches);
$timeoutzman3 = $matches[0];

$client4 = HttpClientBuilder::buildDefault();
$response4 = $client4->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=295530&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body4 = $response4->getBody()->buffer();
$clean_text4 = strip_tags($body4); 
$lines4 = explode("\n", $clean_text4);
foreach ($lines4 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result14 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result24 = $line;
    }
}
$result14 = trim($result14);
$result24 = trim($result24);
preg_match('/\d{2}:\d{2}/', $result14, $matches);
$timeinzman4 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result24, $matches);
$timeoutzman4 = $matches[0];

$numdate1 = "ינואר";
$numdate2 = "פברואר";
$numdate3 = "מרץ";
$numdate4 = "אפריל";
$numdate5 = "מאי";
$numdate6 = "יוני";
$numdate7 = "יולי";
$numdate8 = "אוגוסט";
$numdate9 = "ספטמבר";
$numdate10 = "אוקטובר";
$numdate11 = "נובמבר";
$numdate12 = "דצמבר";

$stringres4 = "$resultline4";
$resultres4 = preg_replace('/\d/', '', $stringres4);
$resultline4new = rtrim($resultres4);
$resultres44 = str_replace(' ', '', $resultline4new);

if($resultres44 == $numdate1){
$hodesh = "01";
}	
if($resultres44 == $numdate2){
$hodesh = "02";
}	
if($resultres44 == $numdate3){
$hodesh = "03";
}	
if($resultres44 == $numdate4){
$hodesh = "04";
}	
if($resultres44 == $numdate5){
$hodesh = "05";
}	
if($resultres44 == $numdate6){
$hodesh = "06";
}	
if($resultres44 == $numdate7){
$hodesh = "07";
}	
if($resultres44 == $numdate8){
$hodesh = "08";
}	
if($resultres44 == $numdate9){
$hodesh = "09";
}	
if($resultres44 == $numdate10){
$hodesh = "10";
}	
if($resultres44 == $numdate11){
$hodesh = "11";
}	
if($resultres44 == $numdate12){
$hodesh = "12";
}	

preg_match_all('!\d+!', $resultline4, $matches);
$numbers = implode(' ', $matches[0]);
$numbersout = $numbers; 
$messageLength = mb_strlen((string)$numbersout);
if($messageLength == 1){
$numbersout = "0".$numbersout;
}
$lastTwoDigits = date('Y');
$dateshabatout = "$numbersout/$hodesh/$lastTwoDigits";

$zmanim = "⌚️ <u><b>זמני כניסת ויציאת השבת:</b></u>

🗓 $updatedLine3$dateshabatout

🕯 <u>זמני כניסת השבת:</u>
ירושלים: <code>$timein</code>
חיפה: <code>$timeinzman2</code>
תל אביב: <code>$timeinzman3</code>
באר שבע: <code>$timeinzman4</code>

🍷 <u>זמני יציאת השבת:</u>
ירושלים: <code>$timeout</code>
חיפה: <code>$timeoutzman2</code>
תל אביב: <code>$timeoutzman3</code>
באר שבע: <code>$timeoutzman4</code>"; 

$me = $this->getSelf();
$me_username = $me['username'];
$bot_API_markup[] = [['text'=>"שתף את הרובוט 🤳",'url'=>"http://t.me/share/url?url=https://t.me/$me_username"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$this->messages->editMessage(peer: $message->chatId, id: $sentMessage2, message: "$zmanim", reply_markup: $bot_API_markup, parse_mode: 'HTML');

} catch (Throwable $e) {
$error = $e->getMessage();
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$this->messages->editMessage(peer: $message->chatId, id: $sentMessage2, message: $error, reply_markup: $bot_API_markup, parse_mode: 'HTML');
}
	}
	
 public function onUpdateBotInlineQuery($update)
    {
try {
$client1 = HttpClientBuilder::buildDefault();
$response1 = $client1->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=281184&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body1 = $response1->getBody()->buffer();
$clean_text1 = strip_tags($body1); 
$lines1 = explode("\n", $clean_text1);
foreach ($lines1 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result11 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result21 = $line;
    }
    if (strpos($line, "פרשת") !== false) {
$result31 = $line;
}
}

$result11 = trim($result11);
$result21 = trim($result21);
$result31 = trim($result31);

preg_match('/\d{2}:\d{2}/', $result11, $matches);
$timein = $matches[0];
preg_match('/\d{2}:\d{2}/', $result21, $matches);
$timeout = $matches[0];

if($result31 != null){
$updatedLine3 = str_ireplace("this week’s Torah portion is", "", $result31);
if (preg_match('/^\s/', $updatedLine3)) {
$updatedLine3 = ltrim($updatedLine3, ' ');
}
$updatedLine3 = rtrim($updatedLine3);
$updatedLine3 = "$updatedLine3 | ";
}
if($result31 == null){
$updatedLine3 = null;
}

$resultline4 = strstr($result21, ',');
$resultline4 = str_ireplace(",", "", $resultline4);

if (preg_match('/^\s/', $resultline4)) {
$resultline4 = ltrim($resultline4, ' ');
}
$resultline4 = rtrim($resultline4);


$client2 = HttpClientBuilder::buildDefault();
$response2 = $client2->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=294801&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body2 = $response2->getBody()->buffer();
$clean_text2 = strip_tags($body2); 
$lines2 = explode("\n", $clean_text2);
foreach ($lines2 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result12 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result22 = $line;
    }
}
$result12 = trim($result12);
$result22 = trim($result22);
preg_match('/\d{2}:\d{2}/', $result12, $matches);
$timeinzman2 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result22, $matches);
$timeoutzman2 = $matches[0];

$client3 = HttpClientBuilder::buildDefault();
$response3 = $client3->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=293397&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body3 = $response3->getBody()->buffer();
$clean_text3 = strip_tags($body3); 
$lines3 = explode("\n", $clean_text3);
foreach ($lines3 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result13 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result23 = $line;
    }
}
$result13 = trim($result13);
$result23 = trim($result23);
preg_match('/\d{2}:\d{2}/', $result13, $matches);
$timeinzman3 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result23, $matches);
$timeoutzman3 = $matches[0];

$client4 = HttpClientBuilder::buildDefault();
$response4 = $client4->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=295530&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body4 = $response4->getBody()->buffer();
$clean_text4 = strip_tags($body4); 
$lines4 = explode("\n", $clean_text4);
foreach ($lines4 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result14 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result24 = $line;
    }
}
$result14 = trim($result14);
$result24 = trim($result24);
preg_match('/\d{2}:\d{2}/', $result14, $matches);
$timeinzman4 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result24, $matches);
$timeoutzman4 = $matches[0];

$numdate1 = "ינואר";
$numdate2 = "פברואר";
$numdate3 = "מרץ";
$numdate4 = "אפריל";
$numdate5 = "מאי";
$numdate6 = "יוני";
$numdate7 = "יולי";
$numdate8 = "אוגוסט";
$numdate9 = "ספטמבר";
$numdate10 = "אוקטובר";
$numdate11 = "נובמבר";
$numdate12 = "דצמבר";

$stringres4 = "$resultline4";
$resultres4 = preg_replace('/\d/', '', $stringres4);
$resultline4new = rtrim($resultres4);
$resultres44 = str_replace(' ', '', $resultline4new);

if($resultres44 == $numdate1){
$hodesh = "01";
}	
if($resultres44 == $numdate2){
$hodesh = "02";
}	
if($resultres44 == $numdate3){
$hodesh = "03";
}	
if($resultres44 == $numdate4){
$hodesh = "04";
}	
if($resultres44 == $numdate5){
$hodesh = "05";
}	
if($resultres44 == $numdate6){
$hodesh = "06";
}	
if($resultres44 == $numdate7){
$hodesh = "07";
}	
if($resultres44 == $numdate8){
$hodesh = "08";
}	
if($resultres44 == $numdate9){
$hodesh = "09";
}	
if($resultres44 == $numdate10){
$hodesh = "10";
}	
if($resultres44 == $numdate11){
$hodesh = "11";
}	
if($resultres44 == $numdate12){
$hodesh = "12";
}	

preg_match_all('!\d+!', $resultline4, $matches);
$numbers = implode(' ', $matches[0]);
$numbersout = $numbers; 
$messageLength = mb_strlen((string)$numbersout);
if($messageLength == 1){
$numbersout = "0".$numbersout;
}
$lastTwoDigits = date('Y');
$dateshabatout = "$numbersout/$hodesh/$lastTwoDigits";

$zmanim = "⌚️ <u><b>זמני כניסת ויציאת השבת:</b></u>

🗓 $updatedLine3$dateshabatout

🕯 <u>זמני כניסת השבת:</u>
ירושלים: <code>$timein</code>
חיפה: <code>$timeinzman2</code>
תל אביב: <code>$timeinzman3</code>
באר שבע: <code>$timeinzman4</code>

🍷 <u>זמני יציאת השבת:</u>
ירושלים: <code>$timeout</code>
חיפה: <code>$timeoutzman2</code>
תל אביב: <code>$timeoutzman3</code>
באר שבע: <code>$timeoutzman4</code>"; 

$me = $this->getSelf();
$me_username = $me['username'];
$bot_API_markup[] = [['text'=>"שתף את הרובוט 🤳",'url'=>"http://t.me/share/url?url=https://t.me/$me_username"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$botInlineMessageText = ['_' => 'inputBotInlineMessageText', 'message' => "$zmanim", 'parse_mode'=> 'HTML', 'reply_markup' => $bot_API_markup];
$inputBotInlineResult = ['_' => 'botInlineResult', 'id' => '0', 'type' => 'article', 'title' => 'זמני כניסת השבת', 'description' => 'לחץ כאן לשיתוף זמני השבת!', 'send_message' => $botInlineMessageText];
		  
        $this->logger("Got query ".$update['query']);
        try {
            $result = ['query_id' => $update['query_id'], 'results' => [$inputBotInlineResult], 'cache_time' => 0];


            if ($update['query'] === 'shabat') {
$this->messages->setInlineBotResults($result);
            } else {
$this->messages->setInlineBotResults($result);
            }
        } catch (Throwable $e) {
            try {
//$this->messages->sendMessage(['peer' => self::ADMIN, 'message' => $e->getCode().': '.$e->getMessage().PHP_EOL.$e->getTraceAsString()]);
$this->messages->sendMessage(['peer' => $update['user_id'], 'message' => $e->getCode().': '.$e->getMessage().PHP_EOL.$e->getTraceAsString()]);
} catch (RPCErrorException $e) {
$this->logger($e);
} catch (Exception $e) {
$this->logger($e);
}

}

} catch (Throwable $e) {
$error = $e->getMessage();
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$sentMessage = $this->messages->sendMessage(peer: $update['query_id'], message: $error, reply_markup: $bot_API_markup);

}
}
	
   #[FilterButtonQueryData('סגור')]
public function closecommand(callbackQuery $query)
{
$msgid = $query->messageId;
try {
$this->messages->deleteMessages(revoke: true, id: [$msgid]); 
} catch (Throwable $e) {
}
}

    #[FilterCommandCaseInsensitive('add')]
    public function addgroupCommand(Incoming & GroupMessage  $message): void
    {
try {
$senderid = $message->senderId;
$messageid = $message->id;
$chatid = $message->chatId;
$me = $this->getSelf();
$me_name = $me['first_name'];
$me_id = $me['id'];

$Chat_Full = $this->getInfo($message->chatId);
$title = $Chat_Full['Chat']['title']?? null;
if($title == null){
$title = "(null)";
}

$admrgh = $Chat_Full['Chat']['admin_rights']['ban_users']?? null;

$type = $Chat_Full['type'];

if($type != "supergroup"){
$txtbot = "<b>אני פועל רק בקבוצות-על(supergroup)</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}

if($type == "supergroup"){

try {
$channelpart = $this->channels->getParticipant(['channel' => $chatid, 'participant' => $message->senderId ]);
if(isset($channelpart['participant']['_'])&& ($channelpart['participant']['_'] == 'channelParticipantAdmin' or $channelpart['participant']['_'] == 'channelParticipantCreator'))  $isadmin = true;
else $isadmin = false;
} catch (Throwable $e) {
$isadmin = false;
}

if($isadmin != false){
	
try {
$channelpart2 = $this->channels->getParticipant(['channel' => $chatid, 'participant' => $me_id ]);
if(isset($channelpart2['participant']['_'])&& ($channelpart2['participant']['_'] == 'channelParticipantAdmin' or $channelpart2['participant']['_'] == 'channelParticipantCreator'))  $isadmin2 = true;
else $isadmin2 = false;	
} catch (Throwable $e) {
$isadmin2 = false;
}

if($isadmin2 != false){

if($admrgh == null){
$txtbot = "<b>אין לי הרשאות ניהול מתאימות.</b>
(הרשאות לחסימת משתמשים ושינוי הרשאות)";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}
if($admrgh != null){
	
	



if (file_exists(__DIR__."/"."data/DBgroups.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/DBgroups.txt");  
$user1 = explode("\n",$filex);
if(!in_array($chatid,$user1)){
if($filex != null){
$filex = $filex."\n"; 
Amp\File\write(__DIR__."/"."data/DBgroups.txt", "$filex"."$chatid");
$txtbot = "<b>הקבוצה נוספה לבסיס נתונים!</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
    if (!file_exists(__DIR__."/"."data/$chatid")) {
mkdir(__DIR__."/"."data/$chatid");
}
}
if($filex == null){
$filex = null; 
Amp\File\write(__DIR__."/"."data/DBgroups.txt", "$filex"."$chatid");
$txtbot = "<b>הקבוצה נוספה לבסיס נתונים!</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
    if (!file_exists(__DIR__."/"."data/$chatid")) {
mkdir(__DIR__."/"."data/$chatid");
}
}
}
if(in_array($chatid,$user1)){
$txtbot = "<b>הקבוצה כבר בבסיס נתונים!</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}	
}	

if (!file_exists(__DIR__."/"."data/DBgroups.txt")) {
$filex = null; 
Amp\File\write(__DIR__."/"."data/DBgroups.txt", "$filex"."$chatid");
$txtbot = "<b>הקבוצה נוספה לבסיס נתונים!</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
    if (__DIR__."/".!file_exists("data/$chatid")) {
mkdir(__DIR__."/"."data/$chatid");
}
}


}
}
if($isadmin2 != true){
$txtbot = "<b>אני לא מנהל בקבוצה.</b>
(יש להוסיף אותי כמנהל)";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}

}
if($isadmin != true){
$txtbot = "<b>אינך מנהל או יוצר בקבוצה.</b>
רק מנהלים יכולים להוסיף את הקבוצה לבסיס נתונים!";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}



}
} catch (Throwable $e) {
$error = $e->getMessage();
$sentMessage = $this->messages->sendMessage(peer: $message->chatId, message: $error);
}
	}
	
    #[FilterCommandCaseInsensitive('remove')]
    public function removegroupCommand(Incoming & GroupMessage  $message): void
    {
try {
$senderid = $message->senderId;
$messageid = $message->id;
$chatid = $message->chatId;
$me = $this->getSelf();
$me_name = $me['first_name'];
$me_id = $me['id'];
$Chat_Full = $this->getInfo($message->chatId);
$title = $Chat_Full['Chat']['title']?? null;
if($title == null){
$title = "(null)";
}

$admrgh = $Chat_Full['Chat']['admin_rights']['ban_users']?? null;

$type = $Chat_Full['type'];

if($type != "supergroup"){
$txtbot = "<b>אני פועל רק בקבוצות-על(supergroup)</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}


if($type == "supergroup"){

try {
$channelpart = $this->channels->getParticipant(['channel' => $chatid, 'participant' => $message->senderId ]);
if(isset($channelpart['participant']['_'])&& ($channelpart['participant']['_'] == 'channelParticipantAdmin' or $channelpart['participant']['_'] == 'channelParticipantCreator'))  $isadmin = true;
else $isadmin = false;
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/USER_NOT_PARTICIPANT/",$estring)){
$isadmin = false;
}else{
$isadmin = false;
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
$estring = (string) $e;

    if ($e->rpc === 'USER_NOT_PARTICIPANT') {
$isadmin = false;
}else{
$isadmin = false;
}
}


if($isadmin != false){
	
try {
$channelpart2 = $this->channels->getParticipant(['channel' => $chatid, 'participant' => $me_id ]);
if(isset($channelpart2['participant']['_'])&& ($channelpart2['participant']['_'] == 'channelParticipantAdmin' or $channelpart2['participant']['_'] == 'channelParticipantCreator'))  $isadmin2 = true;
else $isadmin2 = false;	
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/USER_NOT_PARTICIPANT/",$estring)){
$isadmin2 = false;
}else{
$isadmin2 = false;
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
$estring = (string) $e;

    if ($e->rpc === 'USER_NOT_PARTICIPANT') {
$isadmin2 = false;
}else{
$isadmin2 = false;
}
}


if($isadmin2 != false){

if($admrgh == null){
$txtbot = "<b>אין לי הרשאות ניהול מתאימות.</b>
(הרשאות לחסימת משתמשים ושינוי הרשאות)";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}
if($admrgh != null){
	
	



if (file_exists(__DIR__."/"."data/DBgroups.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/DBgroups.txt");  
$user1 = explode("\n",$filex);
if(in_array($chatid,$user1)){
$filex = Amp\File\read(__DIR__."/"."data/DBgroups.txt");  
$chatidstring = (string) $chatid;
$result = str_replace($chatidstring,"",$filex);
Amp\File\write(__DIR__."/"."data/DBgroups.txt", $result);

$filex2 = Amp\File\read(__DIR__."/"."data/DBgroups.txt");  
$result2 = preg_replace('/^[ \t]*[\r\n]+/m', '', $filex2);
Amp\File\write(__DIR__."/"."data/DBgroups.txt", $result2);

$txtbot = "<b>הקבוצה הוסרה בהצלחה! אני עוזב את הקבוצה...</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
$this->channels->leaveChannel(channel: $message->chatId );


if (file_exists(__DIR__."/"."data/$chatidstring/alertshabat.txt")) {
unlink(__DIR__."/"."data/$chatidstring/alertshabat.txt");
}
if (file_exists(__DIR__."/"."data/$chatidstring/msgclosermotan.txt")) {
unlink(__DIR__."/"."data/$chatidstring/msgclosermotan.txt");
}


}
if(!in_array($chatid,$user1)){
$txtbot = "<b>הקבוצה כבר הוסרה מבסיס נתונים!</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}	
}	

if (!file_exists(__DIR__."/"."data/DBgroups.txt")) {
$txtbot = "<b>הקבוצה כבר הוסרה מבסיס נתונים!</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}


}
}
if($isadmin2 != true){
$txtbot = "<b>אני לא מנהל בקבוצה.</b>
(יש להוסיף אותי כמנהל)";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}

}
if($isadmin != true){
$txtbot = "<b>אינך מנהל או יוצר בקבוצה.</b>
רק מנהלים יכולים להוסיף את הקבוצה לבסיס נתונים!";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}



}


} catch (Throwable $e) {
$error = $e->getMessage();
$sentMessage = $this->messages->sendMessage(peer: $message->chatId, message: $error);

}
	}
	
    #[FilterCommandCaseInsensitive('settings')]
    public function grupsettingsCommand(Incoming & GroupMessage  $message): void
    {
	try {
$senderid = $message->senderId;
$messageid = $message->id;
$chatid = $message->chatId;
$me = $this->getSelf();
$me_name = $me['first_name'];
$me_id = $me['id'];
$me_username = $me['username'];

$Chat_Full = $this->getInfo($message->chatId);
$title = $Chat_Full['Chat']['title']?? null;
if($title == null){
$title = "(null)";
}

$admrgh = $Chat_Full['Chat']['admin_rights']['ban_users']?? null;

$type = $Chat_Full['type'];

if($type != "supergroup"){
$txtbot = "<b>אני פועל רק בקבוצות-על(supergroup)</b>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}


if($type == "supergroup"){
try {
$channelpart = $this->channels->getParticipant(['channel' => $chatid, 'participant' => $message->senderId ]);
if(isset($channelpart['participant']['_'])&& ($channelpart['participant']['_'] == 'channelParticipantAdmin' or $channelpart['participant']['_'] == 'channelParticipantCreator'))  $isadmin = true;
else $isadmin = false;
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/USER_NOT_PARTICIPANT/",$estring)){
$isadmin = false;
}else{
$isadmin = false;
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
$estring = (string) $e;

    if ($e->rpc === 'USER_NOT_PARTICIPANT') {
$isadmin = false;
}else{
$isadmin = false;
}
}

if($isadmin != false){
	

try {
$channelpart2 = $this->channels->getParticipant(['channel' => $chatid, 'participant' => $me_id ]);
if(isset($channelpart2['participant']['_'])&& ($channelpart2['participant']['_'] == 'channelParticipantAdmin' or $channelpart2['participant']['_'] == 'channelParticipantCreator'))  $isadmin2 = true;
else $isadmin2 = false;	
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/USER_NOT_PARTICIPANT/",$estring)){
$isadmin2 = false;
}else{
$isadmin2 = false;
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
$estring = (string) $e;

    if ($e->rpc === 'USER_NOT_PARTICIPANT') {
$isadmin2 = false;
}else{
$isadmin2 = false;
}
}


if($isadmin2 != false){

if($admrgh == null){
$txtbot = "<b>אין לי הרשאות ניהול מתאימות.</b>
(הרשאות לחסימת משתמשים ושינוי הרשאות)";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}
if($admrgh != null){

if (file_exists(__DIR__."/"."data/DBgroups.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/DBgroups.txt");  
$user1 = explode("\n",$filex);
if(!in_array($chatid,$user1)){
$txtbot = "<b>הקבוצה לא נוספה לבסיס נתונים!</b>
שלח את הפקודה <code>/add</code>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}
if(in_array($chatid,$user1)){
$txtbot2 = "<b>התאם אישית את הרובוט בקבוצה:</b>";
if (!file_exists(__DIR__."/"."data/$chatid/alertshabat.txt")) {
$bot_API_markup[] = [['text'=>"OFF ❌",'callback_data'=>"שלחזמני"],['text'=>"זמני כניסת שבת",'callback_data'=>"הסברזמנישבת"]];
}
if (file_exists(__DIR__."/"."data/$chatid/alertshabat.txt")) {
$bot_API_markup[] = [['text'=>"ON ✅",'callback_data'=>"שלחזמני1"],['text'=>"זמני כניסת שבת",'callback_data'=>"הסברזמנישבת"]];
}
if (!file_exists(__DIR__."/"."data/$chatid/alertshabat2.txt")) {
$bot_API_markup[] = [['text'=>"OFF ❌",'callback_data'=>"הודעותלפניואחרי"],['text'=>"הודעות לפני ואחרי שבת",'callback_data'=>"הסברהודעותלפאח"]];
}
if (file_exists(__DIR__."/"."data/$chatid/alertshabat2.txt")) {
$bot_API_markup[] = [['text'=>"ON ✅",'callback_data'=>"הודעותלפניואחרי1"],['text'=>"הודעות לפני ואחרי שבת",'callback_data'=>"הסברהודעותלפאח"]];
}
$bot_API_markup[] = [['text'=>"הגדר הודעה שתשלח לפני שבת ✏️",'callback_data'=>"הודעתסגירה"]];
$bot_API_markup[] = [['text'=>"הגדר הודעה שתשלח במוצאי שבת ✏️",'callback_data'=>"הודעתפתיחה"]];
$bot_API_markup[] = [['text'=>"↪️ החזר לברירת מחדל",'callback_data'=>"החזרברירתמחדל"]];
$bot_API_markup[] = [['text'=>"סגור ✖️",'callback_data'=>"סגור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
try {
$this->messages->sendMessage(peer: $senderid, message: "$txtbot2", reply_markup: $bot_API_markup, parse_mode: 'HTML');
$txtbot = "<b>פאנל ההגדרות נשלח אליך בהודעה פרטית.</b>";
$bot_API_markup2[] = [['text'=>"לחץ כאן למעבר ⚙️",'url'=>"https://t.me/$me_username"]];
$bot_API_markup2 = [ 'inline_keyboard'=> $bot_API_markup2,];
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", reply_markup: $bot_API_markup2, parse_mode: 'HTML');
} catch (Throwable $e) {
$error = $e->getMessage();
$sentMessage = $this->messages->sendMessage(peer: $message->chatId, message: $error);
}
Amp\File\write(__DIR__."/"."data/$senderid/groupid.txt", "$chatid");
}
}

if (!file_exists(__DIR__."/"."data/DBgroups.txt")) {
$txtbot = "<b>הקבוצה לא נוספה לבסיס נתונים!</b>
שלח את הפקודה <code>/add</code>";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}
}
}
if($isadmin2 != true){
$txtbot = "<b>אני לא מנהל בקבוצה.</b>
(יש להוסיף אותי כמנהל)";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}

}
if($isadmin != true){
$txtbot = "<b>אינך מנהל או יוצר בקבוצה.</b>
רק מנהלים יכולים לפתוח פאנל הגדרות!";
$this->messages->sendMessage(peer: $message->chatId, message: "$txtbot", parse_mode: 'HTML');
}



}

} catch (Throwable $e) {
$error = $e->getMessage();
$sentMessage = $this->messages->sendMessage(peer: $message->chatId, message: $error);

}
	}
	
#[FiltersOr(new FilterCommandCaseInsensitive('add'), new FilterCommandCaseInsensitive('remove'), new FilterCommandCaseInsensitive('settings'))]
    public function ifNotCommands(Incoming & PrivateMessage  $message): void
    {
try {
$messageid = $message->id;

$this->messages->deleteMessages(revoke: true, id: [$messageid]); 

$sentMessage = $this->messages->sendMessage(peer: $message->senderId, message: "❌ <i>פקודה זו יש לשלוח בקבוצה בלבד!</i>", parse_mode: 'HTML');
$sentMessage2 = $this->extractMessageId($sentMessage);

$this->sleep(3);
$this->messages->deleteMessages(revoke: true, id: [$sentMessage2]); 
} catch (Throwable $e) {
}
}

   #[FilterButtonQueryData('חזרהלהגדרות')]
public function backtosettings(callbackQuery $query)
{
try {
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists(__DIR__."/"."data/$userid/groupid.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/$userid/groupid.txt");  
}

$txtbot = "<b>התאם אישית את הרובוט בקבוצה:</b>";
if (!file_exists(__DIR__."/"."data/$filex/alertshabat.txt")) {
$bot_API_markup[] = [['text'=>"OFF ❌",'callback_data'=>"שלחזמני"],['text'=>"זמני כניסת שבת",'callback_data'=>"הסברזמנישבת"]];
}
if (file_exists(__DIR__."/"."data/$filex/alertshabat.txt")) {
$bot_API_markup[] = [['text'=>"ON ✅",'callback_data'=>"שלחזמני1"],['text'=>"זמני כניסת שבת",'callback_data'=>"הסברזמנישבת"]];
}
if (!file_exists(__DIR__."/"."data/$filex/alertshabat2.txt")) {
$bot_API_markup[] = [['text'=>"OFF ❌",'callback_data'=>"הודעותלפניואחרי"],['text'=>"הודעות לפני ואחרי שבת",'callback_data'=>"הסברהודעותלפאח"]];
}
if (file_exists(__DIR__."/"."data/$filex/alertshabat2.txt")) {
$bot_API_markup[] = [['text'=>"ON ✅",'callback_data'=>"הודעותלפניואחרי1"],['text'=>"הודעות לפני ואחרי שבת",'callback_data'=>"הסברהודעותלפאח"]];
}
$bot_API_markup[] = [['text'=>"הגדר הודעה שתשלח לפני שבת ✏️",'callback_data'=>"הודעתסגירה"]];
$bot_API_markup[] = [['text'=>"הגדר הודעה שתשלח במוצאי שבת ✏️",'callback_data'=>"הודעתפתיחה"]];
$bot_API_markup[] = [['text'=>"↪️ החזר לברירת מחדל",'callback_data'=>"החזרברירתמחדל"]];
$bot_API_markup[] = [['text'=>"סגור ✖️",'callback_data'=>"סגור"]];
//$bot_API_markup[] = [['text'=>"הגדרות זמני סגירה ⌚️",'callback_data'=>"לאפעילכרגע"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "$txtbot", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
} catch (Throwable $e) {
}
}

   #[FilterButtonQueryData('החזרברירתמחדל')]
public function defaultset(callbackQuery $query)
{
try {
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
if (file_exists(__DIR__."/"."data/$userid/groupid.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/$userid/groupid.txt");  
if (file_exists(__DIR__."/"."data/$filex/alertshabat.txt")) {
unlink(__DIR__."/"."data/$filex/alertshabat.txt");
}
if (file_exists(__DIR__."/"."data/$filex/alertshabat2.txt")) {
unlink(__DIR__."/"."data/$filex/alertshabat2.txt");
}
if (file_exists(__DIR__."/"."data/$filex/msgclosermotan2.txt")) {
unlink(__DIR__."/"."data/$filex/msgclosermotan2.txt");
}
if (file_exists(__DIR__."/"."data/$filex/msgclosermotan.txt")) {
unlink(__DIR__."/"."data/$filex/msgclosermotan.txt");
}
}
$txtbot = "<b>ההגדרות אופסו לברירת מחדל</b> ⚙️";
$bot_API_markup[] = [['text'=>"חזרה להגדרות",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$query->editText($message = "$txtbot", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
} catch (Throwable $e) {
}
}

 #[FilterButtonQueryData('הסברזמנישבת')]
public function infotimes(callbackQuery $query)
{
try {
$query->answer($message = "האם הקבוצה תקבל מידי יום שישי (בשעה 13:30) הודעה עם זמני כניסת השבת!", $alert = true, $url = null, $cacheTime = 0);		
} catch (Throwable $e) {
}
}

 #[FilterButtonQueryData('הסברהודעותלפאח')]
public function infomessages(callbackQuery $query)
{
try {
$query->answer($message = "האם הקבוצה תקבל מידי יום שישי הודעה שתשלח בערב שבת ובצאת שבת!
• ניתן להשתמש בברירת מחדל.
• וניתן להגדיר הודעה מותאמת אישית.", $alert = true, $url = null, $cacheTime = 0);	
} catch (Throwable $e) {
}	
}

 #[FilterButtonQueryData('שלחזמני')]
public function TimesON(callbackQuery $query)
{
try {
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists(__DIR__."/"."data/$userid/groupid.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/$userid/groupid.txt");  
Amp\File\write(__DIR__."/"."data/$filex/alertshabat.txt", "on");
}

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$query->editText($message = "<b>הקבוצה תקבל מידי יום שישי (בשעה 13:30) הודעה עם זמני כניסת השבת! ✅</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
} catch (Throwable $e) {
}
}

  #[FilterButtonQueryData('שלחזמני1')]
public function TimesOFF(callbackQuery $query)
{
try {
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists(__DIR__."/"."data/$userid/groupid.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/$userid/groupid.txt");  
if (file_exists(__DIR__."/"."data/$filex/alertshabat.txt")) {
unlink(__DIR__."/"."data/$filex/alertshabat.txt");
}
}

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$query->editText($message = "<b>הקבוצה לא תקבל מידי יום שישי (בשעה 13:30) הודעה עם זמני כניסת השבת! ❌</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
} catch (Throwable $e) {
}
}

 #[FilterButtonQueryData('הודעותלפניואחרי')]
public function MessagesON(callbackQuery $query)
{
try {
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists(__DIR__."/"."data/$userid/groupid.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/$userid/groupid.txt");  
Amp\File\write(__DIR__."/"."data/$filex/alertshabat2.txt", "on");
}

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>הקבוצה תקבל מידי יום שישי הודעה שתשלח בערב שבת ובצאת שבת!</b> ✅", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
} catch (Throwable $e) {
}
}

  #[FilterButtonQueryData('הודעותלפניואחרי1')]
public function MessagesOFF(callbackQuery $query)
{
try {
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists(__DIR__."/"."data/$userid/groupid.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/$userid/groupid.txt");  
if (file_exists(__DIR__."/"."data/$filex/alertshabat2.txt")) {
unlink(__DIR__."/"."data/$filex/alertshabat2.txt");
}
}

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>הקבוצה לא תקבל מידי יום שישי הודעה שתשלח בערב שבת ובצאת שבת!</b> ❌", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
} catch (Throwable $e) {
}
}

  #[FilterButtonQueryData('הודעתפתיחה')]
public function OpenMessage(callbackQuery $query)
{
try {
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists(__DIR__."/"."data/$userid/groupid.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/$userid/groupid.txt");  
if (file_exists(__DIR__."/"."data/$filex/msgclosermotan2.txt")) {
$filex2 = Amp\File\read(__DIR__."/"."data/$filex/msgclosermotan2.txt");  	
$CLOSER = self::OPENER;
$TXTSGRIGA = "<b>הוגדרה הודעת פתיחה ✔️</b>";
}
if (!file_exists(__DIR__."/"."data/$filex/msgclosermotan2.txt")) {
$CLOSER = self::OPENER;
$TXTSGRIGA = "<b>לא הוגדרה הודעת פתיחה ✖️ </b>";
}
}


$bot_API_markup[] = [['text'=>"הצג הודעה 👁",'callback_data'=>"הצגהודעתפתיחה"]];
$bot_API_markup[] = [['text'=>"הגדר הודעה ➕",'callback_data'=>"הגדרהודעתפתיחה"]];
$bot_API_markup[] = [['text'=>"חזרה להגדרות",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "כאן תוכל להגדיר הודעת פתיחה מותאמת אישית שתשלח במוצאי שבת כשהקבוצה נפתחת!
----------
$TXTSGRIGA
----------
ברירת מחדל: 
<code>$CLOSER</code>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
    if (file_exists(__DIR__."/"."data/$userid/grs1.txt")) {
unlink(__DIR__."/"."data/$userid/grs1.txt");
}
} catch (Throwable $e) {
}
}

  #[FilterButtonQueryData('הודעתסגירה')]
public function CloseMessage(callbackQuery $query)
{
try {
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists(__DIR__."/"."data/$userid/groupid.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/$userid/groupid.txt");  
if (file_exists(__DIR__."/"."data/$filex/msgclosermotan.txt")) {
$filex2 = Amp\File\read(__DIR__."/"."data/$filex/msgclosermotan.txt");  	
$CLOSER = self::CLOSER;
$TXTSGRIGA = "<b>הוגדרה הודעת סגירה ✔️</b>";
}
if (!file_exists(__DIR__."/"."data/$filex/msgclosermotan.txt")) {
$CLOSER = self::CLOSER;
$TXTSGRIGA = "<b>לא הוגדרה הודעת סגירה ✖️ </b>";
}
}

$bot_API_markup[] = [['text'=>"הצג הודעה 👁",'callback_data'=>"הצגהודעתסגירה"]];
$bot_API_markup[] = [['text'=>"הגדר הודעה ➕",'callback_data'=>"הגדרהודעתסגירה"]];
$bot_API_markup[] = [['text'=>"חזרה להגדרות",'callback_data'=>"חזרהלהגדרות"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "כאן תוכל להגדיר הודעת סגירה מותאמת אישית שתשלח בערב שבת כשהקבוצה נסגרת!
----------
$TXTSGRIGA
----------
ברירת מחדל: 
<code>$CLOSER</code>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
    if (file_exists(__DIR__."/"."data/$userid/grs1.txt")) {
unlink(__DIR__."/"."data/$userid/grs1.txt");
}
} catch (Throwable $e) {
}
}

  #[FilterButtonQueryData('הצגהודעתפתיחה')]
public function ViewOpen(callbackQuery $query)
{
try {
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists(__DIR__."/"."data/$userid/groupid.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/$userid/groupid.txt");  
}


if (!file_exists(__DIR__."/"."data/$filex/msgclosermotan2.txt")) {
$query->answer($message = "לא הוגדרה הודעת פתיחה ✖️", $alert = true, $url = null, $cacheTime = 0);
}
if (file_exists(__DIR__."/"."data/$filex/msgclosermotan2.txt")) {
$txtcloser = Amp\File\read(__DIR__."/"."data/$filex/msgclosermotan2.txt"); 

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"הודעתפתיחה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "$txtcloser", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}
} catch (Throwable $e) {
}
}
 
  #[FilterButtonQueryData('הצגהודעתסגירה')]
public function ViewClose(callbackQuery $query)
{
try {
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists(__DIR__."/"."data/$userid/groupid.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/$userid/groupid.txt");  
}


if (!file_exists(__DIR__."/"."data/$filex/msgclosermotan.txt")) {
$query->answer($message = "לא הוגדרה הודעת סגירה ✖️", $alert = true, $url = null, $cacheTime = 0);
}
if (file_exists(__DIR__."/"."data/$filex/msgclosermotan.txt")) {
$txtcloser = Amp\File\read(__DIR__."/"."data/$filex/msgclosermotan.txt"); 

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"הודעתסגירה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "$txtcloser", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

}
} catch (Throwable $e) {
}
}

  #[FilterButtonQueryData('הגדרהודעתפתיחה')]
public function OpenMessageSet(callbackQuery $query)
{
try {
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup[] = [['text'=>"ביטול",'callback_data'=>"הודעתפתיחה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>שלח לי את הודעת הפתיחה שתרצה להגדיר:</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
$msgqutryid = $query->messageId;
Amp\File\write(__DIR__."/"."data/$userid/messagetodelete.txt", "$msgqutryid");
Amp\File\write(__DIR__."/"."data/$userid/grs1.txt", 'txtsgira2');
} catch (Throwable $e) {
}
}

  #[FilterButtonQueryData('הגדרהודעתסגירה')]
public function CloseMessageSet(callbackQuery $query)
{
try {
$userid = $query->userId;   
$chatid = $query->chatId; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup[] = [['text'=>"ביטול",'callback_data'=>"הודעתסגירה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>שלח לי את הודעת הסגירה שתרצה להגדיר:</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
$msgqutryid = $query->messageId;
Amp\File\write(__DIR__."/"."data/$userid/messagetodelete.txt", "$msgqutryid");
Amp\File\write(__DIR__."/"."data/$userid/grs1.txt", 'txtsgira');
} catch (Throwable $e) {
}
}
 
    #[Handler] 
    public function HandleMessageSet(Incoming & PrivateMessage $message): void
    {
try {
$messagetext = $message->message;
$entities = $message->entities;
$messagefile = $message->media;
$messageid = $message->id;
$senderid = $message->senderId;
$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "null";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "null";
}

    if (file_exists(__DIR__."/"."data/$senderid/grs1.txt")) {
$mediax1 = Amp\File\read(__DIR__."/"."data/$senderid/grs1.txt");    

if($mediax1 == "txtsgira"){
    
if(!preg_match('/^\/([Ss]tart)/',$messagetext)){   
 
if($messagetext == null){

 if (file_exists(__DIR__."/data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read(__DIR__."/data/$senderid/messagetodelete.txt");  
try {
$this->messages->deleteMessages(revoke: true, id: [$filexmsgid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

unlink(__DIR__."/data/$senderid/messagetodelete.txt");
}
 
$bot_API_markup[] = [['text'=>"ביטול",'callback_data'=>"הודעתסגירה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$sentMessage = $this->messages->sendMessage(peer: $senderid, message: "<b>נא לשלוח הודעת טקסט בלבד!</b>", reply_markup: $bot_API_markup, parse_mode: 'HTML');
$sentMessage2 = $this->extractMessageId($sentMessage);
Amp\File\write(__DIR__."/data/$senderid/messagetodelete.txt", "$sentMessage2");

try {
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 

}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

}

if($messagetext != null){
$messageLength = mb_strlen($messagetext);

if($messageLength > 1024) {

 if (file_exists(__DIR__."/data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read(__DIR__."/data/$senderid/messagetodelete.txt");  
try {
$this->messages->deleteMessages(revoke: true, id: [$filexmsgid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

unlink(__DIR__."/data/$senderid/messagetodelete.txt");
}
 
$bot_API_markup[] = [['text'=>"ביטול",'callback_data'=>"הודעתסגירה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$sentMessage = $this->messages->sendMessage(peer: $senderid, message: "<b>נא לשלוח טקסט עד 1024 תווים</b>
כמות התווים ששלחת: $messageLength", reply_markup: $bot_API_markup, parse_mode: 'HTML');
$sentMessage2 = $this->extractMessageId($sentMessage);
Amp\File\write(__DIR__."/data/$senderid/messagetodelete.txt", "$sentMessage2");

try {
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 

}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

} 
else 
{
unlink(__DIR__."/"."data/$senderid/grs1.txt");

if (file_exists(__DIR__."/"."data/$senderid/groupid.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/$senderid/groupid.txt");  
if (file_exists(__DIR__."/"."data/$filex/msgclosermotan.txt")) {
unlink(__DIR__."/"."data/$filex/msgclosermotan.txt");  	
}
$htmlmessage = $this->entitiesToHtml($messagetext, $entities, true);
Amp\File\write(__DIR__."/"."data/$filex/msgclosermotan.txt", "$htmlmessage");
}

$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"הודעתסגירה"]
        ]
    ]
];
$sentMessage = $this->messages->sendMessage(peer: $message->senderId, message: "<b>הודעת הסגירה הוגדרה!</b> ✔️", reply_markup: $bot_API_markup, parse_mode: 'HTML');
try {
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 

}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}
 if (file_exists(__DIR__."/data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read(__DIR__."/data/$senderid/messagetodelete.txt");  
try {
$this->messages->deleteMessages(revoke: true, id: [$filexmsgid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

unlink(__DIR__."/data/$senderid/messagetodelete.txt");
}
 
}


	
}


}



}

if($mediax1 == "txtsgira2"){
    
if(!preg_match('/^\/([Ss]tart)/',$messagetext)){   
 
if($messagetext == null){

 if (file_exists(__DIR__."/data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read(__DIR__."/data/$senderid/messagetodelete.txt");  
try {
$this->messages->deleteMessages(revoke: true, id: [$filexmsgid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

unlink(__DIR__."/data/$senderid/messagetodelete.txt");
}
 
$bot_API_markup[] = [['text'=>"ביטול",'callback_data'=>"הודעתפתיחה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$sentMessage = $this->messages->sendMessage(peer: $senderid, message: "<b>נא לשלוח הודעת טקסט בלבד!</b>", reply_markup: $bot_API_markup, parse_mode: 'HTML');
$sentMessage2 = $this->extractMessageId($sentMessage);
Amp\File\write(__DIR__."/data/$senderid/messagetodelete.txt", "$sentMessage2");

try {
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 

}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

}

if($messagetext != null){
$messageLength = mb_strlen($messagetext);

if($messageLength > 1024) {

 if (file_exists(__DIR__."/data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read(__DIR__."/data/$senderid/messagetodelete.txt");  
try {
$this->messages->deleteMessages(revoke: true, id: [$filexmsgid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

unlink(__DIR__."/data/$senderid/messagetodelete.txt");
}
 
$bot_API_markup[] = [['text'=>"ביטול",'callback_data'=>"הודעתפתיחה"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$sentMessage = $this->messages->sendMessage(peer: $senderid, message: "<b>נא לשלוח טקסט עד 1024 תווים</b>
כמות התווים ששלחת: $messageLength", reply_markup: $bot_API_markup, parse_mode: 'HTML');
$sentMessage2 = $this->extractMessageId($sentMessage);
Amp\File\write(__DIR__."/data/$senderid/messagetodelete.txt", "$sentMessage2");

try {
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 

}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

} 
else 
{
unlink(__DIR__."/"."data/$senderid/grs1.txt");

if (file_exists(__DIR__."/"."data/$senderid/groupid.txt")) {
$filex = Amp\File\read(__DIR__."/"."data/$senderid/groupid.txt");  
if (file_exists(__DIR__."/"."data/$filex/msgclosermotan2.txt")) {
unlink(__DIR__."/"."data/$filex/msgclosermotan2.txt");  	
}
$htmlmessage = $this->entitiesToHtml($messagetext, $entities, true);
Amp\File\write(__DIR__."/"."data/$filex/msgclosermotan2.txt", "$htmlmessage");
}

$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"הודעתפתיחה"]
        ]
    ]
];
$sentMessage = $this->messages->sendMessage(peer: $message->senderId, message: "<b>הודעת הפתיחה הוגדרה!</b> ✔️", reply_markup: $bot_API_markup, parse_mode: 'HTML');
try {
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 

}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}
 if (file_exists(__DIR__."/data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read(__DIR__."/data/$senderid/messagetodelete.txt");  
try {
$this->messages->deleteMessages(revoke: true, id: [$filexmsgid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

unlink(__DIR__."/data/$senderid/messagetodelete.txt");
}
 
}


	
}


}



}

}
} catch (Throwable $e) {
}
}


    #[FilterCommandCaseInsensitive('stats')]
    public function StatsGroups(Incoming $message): void
    {
try {
$chatid = $message->chatId;
$senderid = $message->senderId;
$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "null";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "null";
}

$sentMessage = $this->messages->sendMessage(peer: $message->chatId, message: "<b>אנא המתן... מחשב</b> 📊", parse_mode: 'HTML');	
$sentMessage2 = $this->extractMessageId($sentMessage); 

$dialogs = $this->getDialogIds();
$numFruits = count($dialogs);

$peerList31 = [];
foreach($dialogs as $peer)
{
$info = $this->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "supergroup"){
continue;
}
$peerList31[]=$peer;
$numFruits31 = count($peerList31);
}

$peerList312 = [];
foreach($dialogs as $peer)
{
$info = $this->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "chat"){
continue;
}
$peerList312[]=$peer;
$numFruits312 = count($peerList312);
}

if (!isset($numFruits312)) {
$numFruits312 = 0;
} else {
}
if (!isset($numFruits31)) {
$numFruits31 = 0;
} else {
}

$numFruits3new = $numFruits312 + $numFruits31;
$this->messages->editMessage(peer: $message->chatId, id: $sentMessage2, message: "<b>📊 סך הכל קבוצות שומרות שבת בזכותי:</b> <code>$numFruits3new</code>", parse_mode: 'HTML');

} catch (Throwable $e) {
}
 
}


    #[Cron(period: 60.0)] 
    public function cron1(): void
    {
try {
	
date_default_timezone_set("Asia/Jerusalem");
$TIME = date('H:i');
$DATE = date('d/m/Y');
$today = date("d/m/Y H:i"); 

if (file_exists(__DIR__."/"."systemtimein.txt")) {
$systemtime = Amp\File\read(__DIR__."/"."systemtimein.txt");


$dateTime = DateTime::createFromFormat('H:i', $systemtime);
$dateTime->sub(new DateInterval('PT10M'));
$formattedTime = $dateTime->format('H:i');
}else{
$formattedTime = "null";
}
if (file_exists(__DIR__."/"."systemdatein.txt")) {
$systemdate = Amp\File\read(__DIR__."/"."systemdatein.txt");
}else{
$systemdate = "null";
}
if (file_exists(__DIR__."/"."systemtimeout.txt")) {
$systemtime2 = Amp\File\read(__DIR__."/"."systemtimeout.txt");


$dateTime2 = DateTime::createFromFormat('H:i', $systemtime2);
$dateTime2->sub(new DateInterval('PT0M'));
$formattedTime2 = $dateTime2->format('H:i');
}else{
$formattedTime2 = "null";
}
if (file_exists(__DIR__."/"."systemdateout.txt")) {
$systemdate2 = Amp\File\read(__DIR__."/"."systemdateout.txt");
}else{
$systemdate2 = "null";
}

$TIMER = "$today";
$TIMETOCHECK2 = "$systemdate $formattedTime";
if($TIMER == $TIMETOCHECK2 ){


if (file_exists(__DIR__."/"."data/DBgroups.txt")) {
$userstoasend = Amp\File\read(__DIR__."/"."data/DBgroups.txt");  
$usersArray = explode("\n", $userstoasend);
$usersArray = array_filter($usersArray);
$userstoasend1 = ($usersArray);



foreach ($userstoasend1 as $peer) {
try {
$info = $this->getInfo($peer);
$checkar1 = $info['Chat']['default_banned_rights']['view_messages'];
$checkar2 = $info['Chat']['default_banned_rights']['send_messages'];
$checkar3 = $info['Chat']['default_banned_rights']['send_media'];
$checkar4 = $info['Chat']['default_banned_rights']['send_stickers'];
$checkar5 = $info['Chat']['default_banned_rights']['send_gifs'];
$checkar6 = $info['Chat']['default_banned_rights']['send_games'];
$checkar7 = $info['Chat']['default_banned_rights']['send_inline'];
$checkar8 = $info['Chat']['default_banned_rights']['embed_links'];
$checkar9 = $info['Chat']['default_banned_rights']['send_polls'];
$checkar10 = $info['Chat']['default_banned_rights']['change_info'];
$checkar11 = $info['Chat']['default_banned_rights']['invite_users'];
$checkar12 = $info['Chat']['default_banned_rights']['pin_messages'];
$checkar13 = $info['Chat']['default_banned_rights']['manage_topics'];
$checkar14 = $info['Chat']['default_banned_rights']['send_photos'];
$checkar15 = $info['Chat']['default_banned_rights']['send_videos'];
$checkar16 = $info['Chat']['default_banned_rights']['send_roundvideos'];
$checkar17 = $info['Chat']['default_banned_rights']['send_audios'];
$checkar18 = $info['Chat']['default_banned_rights']['send_voices'];
$checkar19 = $info['Chat']['default_banned_rights']['send_docs'];
$checkar20 = $info['Chat']['default_banned_rights']['send_plain'];
$checkartime = $info['Chat']['default_banned_rights']['until_date'];
if($checkar1 != false){
$checkar1 = "true";
}else{
$checkar1 = "false";
}
if($checkar2 != false){
$checkar2 = "true";
}else{
$checkar2 = "false";
}
if($checkar3 != false){
$checkar3 = "true";
}else{
$checkar3 = "false";
}
if($checkar4 != false){
$checkar4 = "true";
}else{
$checkar4 = "false";
}
if($checkar5 != false){
$checkar5 = "true";
}else{
$checkar5 = "false";
}
if($checkar6 != false){
$checkar6 = "true";
}else{
$checkar6 = "false";
}
if($checkar7 != false){
$checkar7 = "true";
}else{
$checkar7 = "false";
}
if($checkar8 != false){
$checkar8 = "true";
}else{
$checkar8 = "false";
}
if($checkar9 != false){
$checkar9 = "true";
}else{
$checkar9 = "false";
}
if($checkar10 != false){
$checkar10 = "true";
}else{
$checkar10 = "false";
}
if($checkar11 != false){
$checkar11 = "true";
}else{
$checkar11 = "false";
}
if($checkar12 != false){
$checkar12 = "true";
}else{
$checkar12 = "false";
}
if($checkar13 != false){
$checkar13 = "true";
}else{
$checkar13 = "false";
}
if($checkar14 != false){
$checkar14 = "true";
}else{
$checkar14 = "false";
}
if($checkar15 != false){
$checkar15 = "true";
}else{
$checkar15 = "false";
}
if($checkar16 != false){
$checkar16 = "true";
}else{
$checkar16 = "false";
}
if($checkar17 != false){
$checkar17 = "true";
}else{
$checkar17 = "false";
}
if($checkar18 != false){
$checkar18 = "true";
}else{
$checkar18 = "false";
}
if($checkar19 != false){
$checkar19 = "true";
}else{
$checkar19 = "false";
}
if($checkar20 != false){
$checkar20 = "true";
}else{
$checkar20 = "false";
}
$checkartime20 = (string) $checkartime;


try {
Amp\File\write(__DIR__."/"."data/$peer/chatb1.txt",$checkar1."\n".$checkar2."\n".$checkar3."\n".$checkar4."\n".$checkar5."\n".$checkar6."\n".$checkar7."\n".$checkar8."\n".$checkar9."\n".$checkar10."\n".$checkar11."\n".$checkar12."\n".$checkar13."\n".$checkar14."\n".$checkar15."\n".$checkar16."\n".$checkar17."\n".$checkar18."\n".$checkar19."\n".$checkar20."\n".$checkartime20);
} finally {
$this->sleep(0.1);
$chatBannedRights = ['_'                => 'chatBannedRights', 
                    'view_messages'     => false, 
                    'send_messages'     => true, 
                    'send_media'        => true, 
                    'send_stickers'     => true, 
                    'send_gifs'         => true, 
                    'send_games'        => true, 
                    'send_inline'       => true, 
                    'embed_links'       => true, 
                    'send_polls'        => true, 
                    'change_info'       => true, 
                    'invite_users'      => true, 
                    'pin_messages'      => true,
                    'manage_topics'     => true, 
                    'send_photos'       => true, 
                    'send_videos'       => true, 
                    'send_roundvideos'  => true, 
                    'send_audios'       => true, 
                    'send_voices'       => true, 
                    'send_docs'         => true,
                    'send_plain'        => true, 
                    'until_date'        => 0,
                ];
	
$Updates1 = $this->messages->editChatDefaultBannedRights(peer: $peer, banned_rights: $chatBannedRights, );
}

if (file_exists(__DIR__."/"."data/$peer/alertshabat2.txt")) {
if (file_exists(__DIR__."/"."data/$peer/msgclosermotan.txt")) {
$modaha = Amp\File\read(__DIR__."/"."data/$peer/msgclosermotan.txt");  	
}
if (!file_exists(__DIR__."/"."data/$peer/msgclosermotan.txt")) {
$modaha = self::CLOSER;
}

$sendmoadaa1 = $this->messages->sendMessage(peer: $peer, message: $modaha, parse_mode: 'html');

}
$this->sleep(0.1);

} catch (Throwable $e) {
continue;
} 
}



}


}

$TIMER = "$today";
$TIMETOCHECK1 = "$systemdate 13:30";
if($TIMER == $TIMETOCHECK1 ){

if (file_exists(__DIR__."/"."data/DBgroups.txt")) {
$userstoasend = Amp\File\read(__DIR__."/"."data/DBgroups.txt");  
$usersArray = explode("\n", $userstoasend);
$usersArray = array_filter($usersArray);
$userstoasend1 = ($usersArray);

$client1 = HttpClientBuilder::buildDefault();
$response1 = $client1->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=281184&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body1 = $response1->getBody()->buffer();
$clean_text1 = strip_tags($body1); 
$lines1 = explode("\n", $clean_text1);
foreach ($lines1 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result11 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result21 = $line;
    }
    if (strpos($line, "פרשת") !== false) {
$result31 = $line;
}
}

$result11 = trim($result11);
$result21 = trim($result21);
$result31 = trim($result31);

preg_match('/\d{2}:\d{2}/', $result11, $matches);
$timein = $matches[0];
preg_match('/\d{2}:\d{2}/', $result21, $matches);
$timeout = $matches[0];

if($result31 != null){
$updatedLine3 = str_ireplace("this week’s Torah portion is", "", $result31);
if (preg_match('/^\s/', $updatedLine3)) {
$updatedLine3 = ltrim($updatedLine3, ' ');
}
$updatedLine3 = rtrim($updatedLine3);
$updatedLine3 = "$updatedLine3 | ";
}
if($result31 == null){
$updatedLine3 = null;
}

$resultline4 = strstr($result21, ',');
$resultline4 = str_ireplace(",", "", $resultline4);

if (preg_match('/^\s/', $resultline4)) {
$resultline4 = ltrim($resultline4, ' ');
}
$resultline4 = rtrim($resultline4);


$client2 = HttpClientBuilder::buildDefault();
$response2 = $client2->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=294801&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body2 = $response2->getBody()->buffer();
$clean_text2 = strip_tags($body2); 
$lines2 = explode("\n", $clean_text2);
foreach ($lines2 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result12 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result22 = $line;
    }
}
$result12 = trim($result12);
$result22 = trim($result22);
preg_match('/\d{2}:\d{2}/', $result12, $matches);
$timeinzman2 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result22, $matches);
$timeoutzman2 = $matches[0];

$client3 = HttpClientBuilder::buildDefault();
$response3 = $client3->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=293397&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body3 = $response3->getBody()->buffer();
$clean_text3 = strip_tags($body3); 
$lines3 = explode("\n", $clean_text3);
foreach ($lines3 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result13 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result23 = $line;
    }
}
$result13 = trim($result13);
$result23 = trim($result23);
preg_match('/\d{2}:\d{2}/', $result13, $matches);
$timeinzman3 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result23, $matches);
$timeoutzman3 = $matches[0];

$client4 = HttpClientBuilder::buildDefault();
$response4 = $client4->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=295530&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body4 = $response4->getBody()->buffer();
$clean_text4 = strip_tags($body4); 
$lines4 = explode("\n", $clean_text4);
foreach ($lines4 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result14 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result24 = $line;
    }
}
$result14 = trim($result14);
$result24 = trim($result24);
preg_match('/\d{2}:\d{2}/', $result14, $matches);
$timeinzman4 = $matches[0];
preg_match('/\d{2}:\d{2}/', $result24, $matches);
$timeoutzman4 = $matches[0];

$numdate1 = "ינואר";
$numdate2 = "פברואר";
$numdate3 = "מרץ";
$numdate4 = "אפריל";
$numdate5 = "מאי";
$numdate6 = "יוני";
$numdate7 = "יולי";
$numdate8 = "אוגוסט";
$numdate9 = "ספטמבר";
$numdate10 = "אוקטובר";
$numdate11 = "נובמבר";
$numdate12 = "דצמבר";

$stringres4 = "$resultline4";
$resultres4 = preg_replace('/\d/', '', $stringres4);
$resultline4new = rtrim($resultres4);
$resultres44 = str_replace(' ', '', $resultline4new);

if($resultres44 == $numdate1){
$hodesh = "01";
}	
if($resultres44 == $numdate2){
$hodesh = "02";
}	
if($resultres44 == $numdate3){
$hodesh = "03";
}	
if($resultres44 == $numdate4){
$hodesh = "04";
}	
if($resultres44 == $numdate5){
$hodesh = "05";
}	
if($resultres44 == $numdate6){
$hodesh = "06";
}	
if($resultres44 == $numdate7){
$hodesh = "07";
}	
if($resultres44 == $numdate8){
$hodesh = "08";
}	
if($resultres44 == $numdate9){
$hodesh = "09";
}	
if($resultres44 == $numdate10){
$hodesh = "10";
}	
if($resultres44 == $numdate11){
$hodesh = "11";
}	
if($resultres44 == $numdate12){
$hodesh = "12";
}	

preg_match_all('!\d+!', $resultline4, $matches);
$numbers = implode(' ', $matches[0]);
$numbersout = $numbers; 
$messageLength = mb_strlen((string)$numbersout);
if($messageLength == 1){
$numbersout = "0".$numbersout;
}
$lastTwoDigits = date('Y');
$dateshabatout = "$numbersout/$hodesh/$lastTwoDigits";

$zmanim = "⌚️ <u><b>זמני כניסת ויציאת השבת:</b></u>

🗓 $updatedLine3$dateshabatout

🕯 <u>זמני כניסת השבת:</u>
ירושלים: <code>$timein</code>
חיפה: <code>$timeinzman2</code>
תל אביב: <code>$timeinzman3</code>
באר שבע: <code>$timeinzman4</code>

🍷 <u>זמני יציאת השבת:</u>
ירושלים: <code>$timeout</code>
חיפה: <code>$timeoutzman2</code>
תל אביב: <code>$timeoutzman3</code>
באר שבע: <code>$timeoutzman4</code>"; 

$me = $this->getSelf();
$me_username = $me['username'];

foreach ($userstoasend1 as $peer) {
try {
if (file_exists(__DIR__."/"."data/$peer/alertshabat.txt")) {
$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"שתף את הרובוט 🤳",'url'=>"http://t.me/share/url?url=https://t.me/$me_username"]		
			
        ]
    ]
];

$sendmoadaa1 = $this->messages->sendMessage(peer: $peer, message: $zmanim, reply_markup: $bot_API_markup, parse_mode: 'html');
$this->sleep(0.1);
}

} catch (Throwable $e) {
continue;
}
}



}

}

$TIMER = "$today";
$TIMETOCHECK3 = "$systemdate2 $formattedTime2";
if($TIMER == $TIMETOCHECK3 ){

if (file_exists(__DIR__."/"."data/DBgroups.txt")) {
$userstoasend = Amp\File\read(__DIR__."/"."data/DBgroups.txt");  
$usersArray = explode("\n", $userstoasend);
$usersArray = array_filter($usersArray);
$userstoasend1 = ($usersArray);

foreach ($userstoasend1 as $peer) {
try {

if (file_exists(__DIR__."/"."data/$peer/chatb1.txt")) {
$lines = file(__DIR__."/"."data/$peer/chatb1.txt");
$dillerr1 = $lines[0];
$dillerr2 = $lines[1];
$dillerr3 = $lines[2];
$dillerr4 = $lines[3];
$dillerr5 = $lines[4];
$dillerr6 = $lines[5];
$dillerr7 = $lines[6];
$dillerr8 = $lines[7];
$dillerr9 = $lines[8];
$dillerr10 = $lines[9];
$dillerr11 = $lines[10];
$dillerr12 = $lines[11];
$dillerr13 = $lines[12];
$dillerr14 = $lines[13];
$dillerr15 = $lines[14];
$dillerr16 = $lines[15];
$dillerr17 = $lines[16];
$dillerr18 = $lines[17];
$dillerr19 = $lines[18];
$dillerr20 = $lines[19];
$dillerr21 = $lines[20];

$checkarnew1 = filter_var($dillerr1, FILTER_VALIDATE_BOOLEAN);
$checkarnew2 = filter_var($dillerr2, FILTER_VALIDATE_BOOLEAN);
$checkarnew3 = filter_var($dillerr3, FILTER_VALIDATE_BOOLEAN);
$checkarnew4 = filter_var($dillerr4, FILTER_VALIDATE_BOOLEAN);
$checkarnew5 = filter_var($dillerr5, FILTER_VALIDATE_BOOLEAN);
$checkarnew6 = filter_var($dillerr6, FILTER_VALIDATE_BOOLEAN);
$checkarnew7 = filter_var($dillerr7, FILTER_VALIDATE_BOOLEAN);
$checkarnew8 = filter_var($dillerr8, FILTER_VALIDATE_BOOLEAN);
$checkarnew9 = filter_var($dillerr9, FILTER_VALIDATE_BOOLEAN);
$checkarnew10 = filter_var($dillerr10, FILTER_VALIDATE_BOOLEAN);
$checkarnew11 = filter_var($dillerr11, FILTER_VALIDATE_BOOLEAN);
$checkarnew12 = filter_var($dillerr12, FILTER_VALIDATE_BOOLEAN);
$checkarnew13 = filter_var($dillerr13, FILTER_VALIDATE_BOOLEAN);
$checkarnew14 = filter_var($dillerr14, FILTER_VALIDATE_BOOLEAN);
$checkarnew15 = filter_var($dillerr15, FILTER_VALIDATE_BOOLEAN);
$checkarnew16 = filter_var($dillerr16, FILTER_VALIDATE_BOOLEAN);
$checkarnew17 = filter_var($dillerr17, FILTER_VALIDATE_BOOLEAN);
$checkarnew18 = filter_var($dillerr18, FILTER_VALIDATE_BOOLEAN);
$checkarnew19 = filter_var($dillerr19, FILTER_VALIDATE_BOOLEAN);
$checkarnew20 = filter_var($dillerr20, FILTER_VALIDATE_BOOLEAN);
$checkarnew21 = intval($dillerr21);

$chatBannedRights2 = ['_'                => 'chatBannedRights', 
                    'view_messages'     => $checkarnew1,
                    'send_messages'     => $checkarnew2, 
                    'send_media'        => $checkarnew3, 
                    'send_stickers'     => $checkarnew4, 
                    'send_gifs'         => $checkarnew5, 
                    'send_games'        => $checkarnew6, 
                    'send_inline'       => $checkarnew7, 
                    'embed_links'       => $checkarnew8, 
                    'send_polls'        => $checkarnew9, 
                    'change_info'       => $checkarnew10, 
                    'invite_users'      => $checkarnew11, 
                    'pin_messages'      => $checkarnew12,
                    'manage_topics'     => $checkarnew13, 
                    'send_photos'       => $checkarnew14, 
                    'send_videos'       => $checkarnew15, 
                    'send_roundvideos'  => $checkarnew16, 
                    'send_audios'       => $checkarnew17, 
                    'send_voices'       => $checkarnew18, 
                    'send_docs'         => $checkarnew19,
                    'send_plain'        => $checkarnew20, 
                    'until_date'        => $checkarnew21,
                ];

$Updates2 = $this->messages->editChatDefaultBannedRights(peer: $peer, banned_rights: $chatBannedRights2, );
if (file_exists(__DIR__."/"."data/$peer/alertshabat2.txt")) {
if (file_exists(__DIR__."/"."data/$peer/msgclosermotan2.txt")) {
$modaha = Amp\File\read(__DIR__."/"."data/$peer/msgclosermotan2.txt");  	
}
if (!file_exists(__DIR__."/"."data/$peer/msgclosermotan2.txt")) {
$modaha = self::OPENER;
}

$sendmoadaa1 = $this->messages->sendMessage(peer: $peer, message: $modaha, parse_mode: 'html');
}
$this->sleep(0.1);
}
} catch (Throwable $e) {
continue;
}
}


}

}

} catch (Throwable $e) {
}
}

    #[Cron(period: 1800.0)] 
    public function cron4(): void
    {
try {
$client1 = HttpClientBuilder::buildDefault();
$response1 = $client1->request(new Request("https://www.hebcal.com/shabbat?cfg=i2&geonameid=281184&ue=off&M=on&lg=he-x-NoNikud&tgt=_top"));
$body1 = $response1->getBody()->buffer();
$clean_text1 = strip_tags($body1); 
$lines1 = explode("\n", $clean_text1);
foreach ($lines1 as $line) {
    if (strpos($line, "הדלקת נרות") !== false) {
        $result11 = $line;
    }
    if (strpos($line, "הבדלה") !== false) {
        $result21 = $line;
    }
}
$result11 = trim($result11);
$result21 = trim($result21);

preg_match('/\d{2}:\d{2}/', $result11, $matches);
$timein = $matches[0];
preg_match('/\d{2}:\d{2}/', $result21, $matches);
$timeout = $matches[0];

$resultline4 = strstr($result21, ',');
$resultline4 = str_ireplace(",", "", $resultline4);
if (preg_match('/^\s/', $resultline4)) {
$resultline4 = ltrim($resultline4, ' ');
}
$resultline4 = rtrim($resultline4);
$resultline4new89 = strstr($result11, ',');
$resultline4new89 = str_ireplace(",", "", $resultline4new89);
if (preg_match('/^\s/', $resultline4new89)) {
$resultline4new89 = ltrim($resultline4new89, ' ');
}
$resultline4new89 = rtrim($resultline4new89);

$numdate1 = "ינואר";
$numdate2 = "פברואר";
$numdate3 = "מרץ";
$numdate4 = "אפריל";
$numdate5 = "מאי";
$numdate6 = "יוני";
$numdate7 = "יולי";
$numdate8 = "אוגוסט";
$numdate9 = "ספטמבר";
$numdate10 = "אוקטובר";
$numdate11 = "נובמבר";
$numdate12 = "דצמבר";

$stringres4 = "$resultline4";
$resultres4 = preg_replace('/\d/', '', $stringres4);
$resultline4new = rtrim($resultres4);
$resultres44 = str_replace(' ', '', $resultline4new);

if($resultres44 == $numdate1){
$hodesh = "01";
}	
if($resultres44 == $numdate2){
$hodesh = "02";
}	
if($resultres44 == $numdate3){
$hodesh = "03";
}	
if($resultres44 == $numdate4){
$hodesh = "04";
}	
if($resultres44 == $numdate5){
$hodesh = "05";
}	
if($resultres44 == $numdate6){
$hodesh = "06";
}	
if($resultres44 == $numdate7){
$hodesh = "07";
}	
if($resultres44 == $numdate8){
$hodesh = "08";
}	
if($resultres44 == $numdate9){
$hodesh = "09";
}	
if($resultres44 == $numdate10){
$hodesh = "10";
}	
if($resultres44 == $numdate11){
$hodesh = "11";
}	
if($resultres44 == $numdate12){
$hodesh = "12";
}	

preg_match_all('!\d+!', $resultline4, $matches);
$numbers = implode(' ', $matches[0]);

preg_match_all('!\d+!', $resultline4new89, $matches);
$numbers2 = implode(' ', $matches[0]);

$numbersin = $numbers2;
$numbersout = $numbers; 
$messageLength = mb_strlen((string)$numbersout);
if($messageLength == 1){
$numbersout = "0".$numbersout;
}
$messageLength = mb_strlen((string)$numbersin);
if($messageLength == 1){
$numbersin = "0".$numbersin;
}

$lastTwoDigits = date('Y');

$dateshabatin = "$numbersin/$hodesh/$lastTwoDigits";
$timeshabatin = $timein;

$dateshabatout = "$numbersout/$hodesh/$lastTwoDigits";
$timeshabatout = $timeout;

Amp\File\write(__DIR__."/"."systemtimein.txt",$timeshabatin);
Amp\File\write(__DIR__."/"."systemdatein.txt",$dateshabatin);
Amp\File\write(__DIR__."/"."systemtimeout.txt",$timeshabatout);
Amp\File\write(__DIR__."/"."systemdateout.txt",$dateshabatout);
} catch (Throwable $e) {
}
}


    #[FilterCommandCaseInsensitive('donate')]
    public function Payments(Incoming & PrivateMessage  $message): void
    {
$senderid = $message->senderId;
$messageid = $message->id;
try {
$originalString = $senderid;
$encodedString = $originalString;
$labeledPrice1 = ['_' => 'labeledPrice', 'label' => 'star', 'amount' => 5];
$invoice1 = ['_' => 'invoice', 'currency' => 'XTR', 'prices' => [$labeledPrice1],];
$labeledPrice2 = ['_' => 'labeledPrice', 'label' => 'star', 'amount' => 25];
$invoice2 = ['_' => 'invoice', 'currency' => 'XTR', 'prices' => [$labeledPrice2],];
$labeledPrice3 = ['_' => 'labeledPrice', 'label' => 'star', 'amount' => 100];
$invoice3 = ['_' => 'invoice', 'currency' => 'XTR', 'prices' => [$labeledPrice3],];
$labeledPrice4 = ['_' => 'labeledPrice', 'label' => 'star', 'amount' => 150];
$invoice4 = ['_' => 'invoice', 'currency' => 'XTR', 'prices' => [$labeledPrice4],];
$labeledPrice5 = ['_' => 'labeledPrice', 'label' => 'star', 'amount' => 250];
$invoice5 = ['_' => 'invoice', 'currency' => 'XTR', 'prices' => [$labeledPrice5],];
$labeledPrice6 = ['_' => 'labeledPrice', 'label' => 'star', 'amount' => 400];
$invoice6 = ['_' => 'invoice', 'currency' => 'XTR', 'prices' => [$labeledPrice6],];
$inputMediaInvoice1 = ['_' => 'inputMediaInvoice', 'title' => 'תמכו בי', 'description' => 'תמכו בי ב 5 ⭐️', 'invoice' => $invoice1, 'payload' => "$encodedString", 'provider_data' => 'test']; 
$inputMediaInvoice2 = ['_' => 'inputMediaInvoice', 'title' => 'תמכו בי', 'description' => 'תמכו בי ב 25 ⭐️', 'invoice' => $invoice2, 'payload' => "$encodedString", 'provider_data' => 'test']; 
$inputMediaInvoice3 = ['_' => 'inputMediaInvoice', 'title' => 'תמכו בי', 'description' => 'תמכו בי ב 100 ⭐️', 'invoice' => $invoice3, 'payload' => "$encodedString", 'provider_data' => 'test']; 
$inputMediaInvoice4 = ['_' => 'inputMediaInvoice', 'title' => 'תמכו בי', 'description' => 'תמכו בי ב 150 ⭐️', 'invoice' => $invoice4, 'payload' => "$encodedString", 'provider_data' => 'test']; 
$inputMediaInvoice5 = ['_' => 'inputMediaInvoice', 'title' => 'תמכו בי', 'description' => 'תמכו בי ב 250 ⭐️', 'invoice' => $invoice5, 'payload' => "$encodedString", 'provider_data' => 'test']; 
$inputMediaInvoice6 = ['_' => 'inputMediaInvoice', 'title' => 'תמכו בי', 'description' => 'תמכו בי ב 400 ⭐️', 'invoice' => $invoice6, 'payload' => "$encodedString", 'provider_data' => 'test']; 
$payments_ExportedInvoice1 = $this->payments->exportInvoice(invoice_media: $inputMediaInvoice1, );
$urlexp1 = $payments_ExportedInvoice1['url']; 
$payments_ExportedInvoice2 = $this->payments->exportInvoice(invoice_media: $inputMediaInvoice2, );
$urlexp2 = $payments_ExportedInvoice2['url']; 
$payments_ExportedInvoice3 = $this->payments->exportInvoice(invoice_media: $inputMediaInvoice3, );
$urlexp3 = $payments_ExportedInvoice3['url']; 
$payments_ExportedInvoice4 = $this->payments->exportInvoice(invoice_media: $inputMediaInvoice4, );
$urlexp4 = $payments_ExportedInvoice4['url']; 
$payments_ExportedInvoice5 = $this->payments->exportInvoice(invoice_media: $inputMediaInvoice5, );
$urlexp5 = $payments_ExportedInvoice5['url']; 
$payments_ExportedInvoice6 = $this->payments->exportInvoice(invoice_media: $inputMediaInvoice6, );
$urlexp6 = $payments_ExportedInvoice6['url']; 

$bot_API_markup = ['inline_keyboard' => 
    [
        [	
['text'=>"⭐️ 5",'url'=>"$urlexp1"],['text'=>"⭐️ 25",'url'=>"$urlexp2"],['text'=>"⭐️ 100",'url'=>"$urlexp3"]
                    ],
                    [	
['text'=>"⭐️ 150",'url'=>"$urlexp4"],['text'=>"⭐️ 250",'url'=>"$urlexp5"],['text'=>"⭐️ 400",'url'=>"$urlexp6"]
        ]
    ]
];

$inputReplyToMessage = ['_' => 'inputReplyToMessage', 'reply_to_msg_id' => $messageid];
$this->messages->sendMessage(no_webpage: true, peer: $message->senderId, reply_to: $inputReplyToMessage, message: "היי, תודה שאתם רוצים לתרום לי🥰
בחרו את סכום התרומה שתרצו לתת👇", reply_markup: $bot_API_markup, parse_mode: 'HTML', effect: 5159385139981059251) ;
} catch (Throwable $e) {
$error = $e->getMessage();
$sentMessage = $this->messages->sendMessage(peer: $message->senderId, message: $error);
}
}

public function onupdateBotPrecheckoutQuery($update)
    {
try {
$userid = $update['user_id'];
$total_amount = $update['total_amount']; 
$query_id = $update['query_id']; 
$payload = $update['payload']; 
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

try {
$usernames = $User_Full['User']['usernames']?? null;
$newLangsCommausername = null;
$peerList2username = [];
foreach ($usernames as $username) {
$usernamexfr = $username['username'];
$usernamexfr = "@".$usernamexfr;
$peerList2username[]=$usernamexfr;
}
$newLangsCommausername = implode(" ", $peerList2username);
}catch (\danog\MadelineProto\Exception $e) {
} catch (\danog\MadelineProto\RPCErrorException $e) {
}
$username = $User_Full['User']['username']?? null;
if($username == null){	
if($newLangsCommausername != null){
$username = $newLangsCommausername;
}else{
$username = "(null)";
}
}else{
$username = "@".$username;
}

$sucses = $this->messages->setBotPrecheckoutResults(success: true, query_id: $query_id);

if($sucses == true){
$this->messages->sendMessage(peer: $userid, message: "<b>סכום:</b> $total_amount ⭐️
🎉 תודה על תרומתך 🎉", parse_mode: 'HTML', effect: 5159385139981059251);	

$this->sendMessageToAdmins("<b>תרומה התקבלה! 🎉</b>
FIRSTNAME: <a href='mention:$userid'>$first_name </a>
ID: <a href='mention:$userid'>$userid </a>
USERNAME: $username
<b>סכום:</b> $total_amount ⭐️",parseMode: ParseMode::HTML);
}

} catch (Throwable $e) {
$error = $e->getMessage();
}
}

//////////////////////// ADMIN COMMANDS

#[FilterCommandCaseInsensitive('admin')]
public function admincommand(Incoming & PrivateMessage & FromAdmin $message): void
    {
$senderid = $message->senderId;
$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "null";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "null";
}


$bot_API_markup[] = [['text'=>"סטטיסטיקות מנויים 📊",'callback_data'=>"סטטיסטיקות"]];
$bot_API_markup[] = [['text'=>"הצג מנויים 👁",'callback_data'=>"רשימתמשתמשים"]];
$bot_API_markup[] = [['text'=>"שידור למנויים 📮",'callback_data'=>"שידורלמשתמשים"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$this->messages->sendMessage(peer: $message->senderId, message: "<b>ברוך הבא מנהל! 👋</b>", reply_markup: $bot_API_markup, parse_mode: 'HTML');
    if (file_exists("data/$senderid/grs1.txt")) {
unlink("data/$senderid/grs1.txt");
}
    }

#[FilterButtonQueryData('חזרהמנהל')] 
public function addsohe1hazor(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$flag = false;
$ADMIN = $this->getAdminIds();
foreach ($ADMIN as $user) {
$usertocheck = (string) $user;
$senderidtocheck = (string) $userid;
if (preg_match('/^' . preg_quote($senderidtocheck, '/') . '\b/i', $usertocheck)) {
$flag = true;
break;
}
}

if($flag){ 

$bot_API_markup[] = [['text'=>"סטטיסטיקות מנויים 📊",'callback_data'=>"סטטיסטיקות"]];
$bot_API_markup[] = [['text'=>"הצג מנויים 👁",'callback_data'=>"רשימתמשתמשים"]];
$bot_API_markup[] = [['text'=>"שידור למנויים 📮",'callback_data'=>"שידורלמשתמשים"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];


$query->editText($message = "<b>ברוך הבא מנהל! 👋</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
if (file_exists("data/$userid/grs1.txt")) {
unlink("data/$userid/grs1.txt");
}
}
}

#[FilterButtonQueryData('חזרהמנהל2')] 
public function addsohe1hazor2(callbackQuery $query)
{
$userid = $query->userId;  
$msgid = $query->messageId;  

$flag = false;
$ADMIN = $this->getAdminIds();
foreach ($ADMIN as $user) {
$usertocheck = (string) $user;
$senderidtocheck = (string) $userid;
if (preg_match('/^' . preg_quote($senderidtocheck, '/') . '\b/i', $usertocheck)) {
$flag = true;
break;
}
}

if($flag){ 

try {
$this->messages->deleteMessages(revoke: true, id: [$msgid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

$bot_API_markup[] = [['text'=>"סטטיסטיקות מנויים 📊",'callback_data'=>"סטטיסטיקות"]];
$bot_API_markup[] = [['text'=>"הצג מנויים 👁",'callback_data'=>"רשימתמשתמשים"]];
$bot_API_markup[] = [['text'=>"שידור למנויים 📮",'callback_data'=>"שידורלמשתמשים"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];


$this->messages->sendMessage(peer: $userid, message: "<b>ברוך הבא מנהל! 👋</b>", reply_markup: $bot_API_markup, parse_mode: 'HTML');

if (file_exists(__DIR__."/data/$userid/grs1.txt")) {
unlink(__DIR__."/data/$userid/grs1.txt");
}

    if (file_exists(__DIR__."/data/BUTTONS.txt")) {
unlink(__DIR__."/data/BUTTONS.txt");  
	}	
 if (file_exists(__DIR__."/data/$userid/txt.txt")) {
unlink(__DIR__."/data/$userid/txt.txt");  
}
  if (file_exists(__DIR__."/data/$userid/ent.txt")) {
unlink(__DIR__."/data/$userid/ent.txt");  
  }	  
  if (file_exists(__DIR__."/data/$userid/media.txt")) {
unlink(__DIR__."/data/$userid/media.txt");  
  }	 

}

}

#[FilterButtonQueryData('סטטיסטיקות')] 
public function statsusers(callbackQuery $query)
{
try {
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
   $bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]
        ]
    ]
];

$query->editText($message = "<b>אנא המתן... מחשב 📊</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);


$dialogs = $this->getDialogIds();
$numFruits = count($dialogs);

$peerList2 = [];
foreach($dialogs as $peer)
{
try {
$info = $this->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "channel"){
continue;
}
$peerList2[]=$peer;
$numFruits2 = count($peerList2);
}catch (\danog\MadelineProto\Exception $e) {
continue;
} catch (\danog\MadelineProto\RPCErrorException $e) {
continue;
}
}

$peerList31 = [];
foreach($dialogs as $peer)
{
try {
$info = $this->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "supergroup"){
continue;
}
$peerList31[]=$peer;
$numFruits31 = count($peerList31);
}catch (\danog\MadelineProto\Exception $e) {
continue;
} catch (\danog\MadelineProto\RPCErrorException $e) {
continue;
}
}

$peerList312 = [];
foreach($dialogs as $peer)
{
try {
$info = $this->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "chat"){
continue;
}
$peerList312[]=$peer;
$numFruits312 = count($peerList312);
}catch (\danog\MadelineProto\Exception $e) {
continue;
} catch (\danog\MadelineProto\RPCErrorException $e) {
continue;
}
}


$peerListbots = [];
foreach($dialogs as $peer)
{
try {
$info = $this->getInfo($peer);
if(!isset($info['type']) || $info['type'] != "bot"){
continue;
}
$peerListbots[]=$peer;
$numFruitsbots = count($peerListbots);
}catch (\danog\MadelineProto\Exception $e) {
continue;
} catch (\danog\MadelineProto\RPCErrorException $e) {
continue;
}
}



if (!isset($numFruits312)) {
$numFruits312 = 0;
} else {
}
if (!isset($numFruits31)) {
$numFruits31 = 0;
} else {
}
if (!isset($numFruits2)) {
$numFruits2 = 0;
} else {
}
if (!isset($numFruitsbots)) {
$numFruitsbots = 0;
} else {
}


$numFruits3new = $numFruits2 + $numFruits312 + $numFruits31 + $numFruitsbots;
$numofall = $numFruits - $numFruits3new;


$query->editText($message = "<b>🧮 סטטיסטיקות מנויים 📊</b>
- - - - - - - - - -
כמות ערוצים: $numFruits2
כמות קבוצות: $numFruits312
כמות קבוצות-על: $numFruits31
כמות בוטים: $numFruitsbots
כמות משתמשים: $numofall
- - - - - - - - - -
סך הכל מנויים: $numFruits", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
} catch (Throwable $e) {
}
}

#[FilterButtonQueryData('רשימתמשתמשים')] 
public function reshimamishtamshim(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$dialogs = $this->getDialogIds();
$newLangsComma = implode("\n", $dialogs);
Amp\File\write(__DIR__."/ids.txt",$newLangsComma);

    if (file_exists(__DIR__."/ids.txt")) {
$filex = Amp\File\read(__DIR__."/ids.txt");
$numFruits = count($dialogs);

if($filex != null){
$file = __DIR__."/ids.txt";
$outputFile = __DIR__."/idsnew.txt"; 
$startLine = 1; 
$endLine = 5; 
$lines = file($file); 
$selectedLines = array_slice($lines, $startLine - 1, $endLine - $startLine + 1);
Amp\File\write($outputFile, implode("", $selectedLines));
$outputFilex = Amp\File\read(__DIR__."/idsnew.txt"); 

Amp\File\write(__DIR__."/data/startline_cat.txt","$startLine");
Amp\File\write(__DIR__."/data/endline_cat.txt","$endLine");
Amp\File\write(__DIR__."/data/page_var.txt","1");

$category = array_filter(array_map('trim', explode("\n", $outputFilex)));

$resultpages = ceil($numFruits / 5);

$bot_API_markup = [];

foreach($category as $txt) {
try {
	
$User_Full2 = $this->getInfo($txt);
$type = $User_Full2['type']?? null;

if($type == "channel" || "supergroup" || "chat"){
$first_name2 = $User_Full2['Chat']['title']?? null;
if($first_name2 == null){
$first_name2 = "$txt";
}
}

if($type == "user" || "bot"){
$first_name2 = $User_Full2['User']['first_name']?? null;
if($first_name2 == null){
$first_name2 = "$txt";
}
}

}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/This peer is not present in the internal peer database/",$estring)){
$first_name2 = "$txt";
}
}

    $bot_API_markup[] = [['text' => $first_name2, 'callback_data' => "openprofile_$txt"]];
}
if($numFruits > $endLine){
$bot_API_markup[] = [['text'=>"הבא",'callback_data'=>"רשימתמנוייםהמשך"]];
}
if($startLine > 5){
$bot_API_markup[] = [['text'=>"הקודם",'callback_data'=>"רשימתמנוייםהקודם"]];
}
$bot_API_markup[] = [['text'=>"דף 1/$resultpages 📃",'callback_data'=>"var_cat"]];
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]];
$bot_API_markup = ['inline_keyboard' => $bot_API_markup];

$query->editText($message = "⛓️‍💥 <b>סך הכל מנויים:</b> $numFruits", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

if($filex == null){
$newLangsComma = "אין עדיין משתמשים..";
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>$newLangsComma</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

	}	
    if (!file_exists(__DIR__."/ids.txt")) {
if($filex == null){
$newLangsComma = "אין עדיין משתמשים..";
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>$newLangsComma</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

	}		





}

#[FilterButtonQueryData('רשימתמנוייםהמשך')] 
public function reshimamishtamshim2(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$dialogs = $this->getDialogIds();
$newLangsComma = implode("\n", $dialogs);
Amp\File\write(__DIR__."/ids.txt",$newLangsComma);

    if (file_exists(__DIR__."/ids.txt")) {
$filex = Amp\File\read(__DIR__."/ids.txt");
$numFruits = count($dialogs);

if($filex != null){
$startx = Amp\File\read(__DIR__."/data/startline_cat.txt"); 
$endx = Amp\File\read(__DIR__."/data/endline_cat.txt"); 



$file = __DIR__."/ids.txt";
$outputFile = __DIR__."/idsnew.txt"; 
$startLine = $startx + 5; 
$endLine = $endx + 5; 
$lines = file($file); 
$selectedLines = array_slice($lines, $startLine - 1, $endLine - $startLine + 1);
Amp\File\write($outputFile, implode("", $selectedLines));
$outputFilex = Amp\File\read(__DIR__."/idsnew.txt"); 

$pages_varx = Amp\File\read(__DIR__."/data/page_var.txt"); 
$pages_var = $pages_varx + 1; 

Amp\File\write(__DIR__."/data/startline_cat.txt","$startLine");
Amp\File\write(__DIR__."/data/endline_cat.txt","$endLine");
Amp\File\write(__DIR__."/data/page_var.txt","$pages_var");

$category = array_filter(array_map('trim', explode("\n", $outputFilex)));

$resultpages = ceil($numFruits / 5);

$bot_API_markup = [];

foreach($category as $txt) {
try {
	
$User_Full2 = $this->getInfo($txt);
$type = $User_Full2['type']?? null;

if($type == "channel" || "supergroup" || "chat"){
$first_name2 = $User_Full2['Chat']['title']?? null;
if($first_name2 == null){
$first_name2 = "$txt";
}
}

if($type == "user" || "bot"){
$first_name2 = $User_Full2['User']['first_name']?? null;
if($first_name2 == null){
$first_name2 = "$txt";
}
}

}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/This peer is not present in the internal peer database/",$estring)){
$first_name2 = "$txt";
}
}

    $bot_API_markup[] = [['text' => $first_name2, 'callback_data' => "openprofile_$txt"]];
}
if($numFruits > $endLine){
$bot_API_markup[] = [['text'=>"הבא",'callback_data'=>"רשימתמנוייםהמשך"]];
}
if($startLine > 5){
$bot_API_markup[] = [['text'=>"הקודם",'callback_data'=>"רשימתמנוייםהקודם"]];
}
$bot_API_markup[] = [['text'=>"דף $pages_var/$resultpages 📃",'callback_data'=>"var_cat"]];
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]];
$bot_API_markup = ['inline_keyboard' => $bot_API_markup];

$query->editText($message = "⛓️‍💥 <b>סך הכל מנויים:</b> $numFruits", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

if($filex == null){
$newLangsComma = "אין עדיין משתמשים..";
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>$newLangsComma</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

	}	
    if (!file_exists(__DIR__."/ids.txt")) {
if($filex == null){
$newLangsComma = "אין עדיין משתמשים..";
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>$newLangsComma</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

	}		
		
		
}

#[FilterButtonQueryData('רשימתמנוייםהקודם')] 
public function reshimamishtamshim3(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$dialogs = $this->getDialogIds();
$newLangsComma = implode("\n", $dialogs);
Amp\File\write(__DIR__."/ids.txt",$newLangsComma);

    if (file_exists(__DIR__."/ids.txt")) {
$filex = Amp\File\read(__DIR__."/ids.txt");
$numFruits = count($dialogs);

if($filex != null){
$startx = Amp\File\read(__DIR__."/data/startline_cat.txt"); 
$endx = Amp\File\read(__DIR__."/data/endline_cat.txt"); 

$file = __DIR__."/ids.txt";
$outputFile = __DIR__."/idsnew.txt"; 
$startLine = $startx - 5; 
$endLine = $endx - 5; 
$lines = file($file); 
$selectedLines = array_slice($lines, $startLine - 1, $endLine - $startLine + 1);
Amp\File\write($outputFile, implode("", $selectedLines));
$outputFilex = Amp\File\read(__DIR__."/idsnew.txt"); 

$pages_varx = Amp\File\read(__DIR__."/data/page_var.txt"); 
$pages_var = $pages_varx - 1; 

Amp\File\write(__DIR__."/data/startline_cat.txt","$startLine");
Amp\File\write(__DIR__."/data/endline_cat.txt","$endLine");
Amp\File\write(__DIR__."/data/page_var.txt","$pages_var");

$category = array_filter(array_map('trim', explode("\n", $outputFilex)));

$resultpages = ceil($numFruits / 5);

$bot_API_markup = [];

foreach($category as $txt) {
try {
	
$User_Full2 = $this->getInfo($txt);
$type = $User_Full2['type']?? null;

if($type == "channel" || "supergroup" || "chat"){
$first_name2 = $User_Full2['Chat']['title']?? null;
if($first_name2 == null){
$first_name2 = "$txt";
}
}

if($type == "user" || "bot"){
$first_name2 = $User_Full2['User']['first_name']?? null;
if($first_name2 == null){
$first_name2 = "$txt";
}
}

}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/This peer is not present in the internal peer database/",$estring)){
$first_name2 = "$txt";
}
}


    $bot_API_markup[] = [['text' => $first_name2, 'callback_data' => "openprofile_$txt"]];
}
if($numFruits > $endLine){
$bot_API_markup[] = [['text'=>"הבא",'callback_data'=>"רשימתמנוייםהמשך"]];
}
if($startLine > 5){
$bot_API_markup[] = [['text'=>"הקודם",'callback_data'=>"רשימתמנוייםהקודם"]];
}
$bot_API_markup[] = [['text'=>"דף $pages_var/$resultpages 📃",'callback_data'=>"var_cat"]];
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]];
$bot_API_markup = ['inline_keyboard' => $bot_API_markup];

$query->editText($message = "⛓️‍💥 <b>סך הכל מנויים:</b> $numFruits", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

if($filex == null){
$newLangsComma = "אין עדיין משתמשים..";
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>$newLangsComma</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

	}	
    if (!file_exists(__DIR__."/ids.txt")) {
if($filex == null){
$newLangsComma = "אין עדיין משתמשים..";
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>$newLangsComma</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

	}		
	
}

#[FilterButtonQueryData('var_cat')]
public function var_cat_command(callbackQuery $query)
{  
    if (file_exists(__DIR__."/ids.txt")) {
$filex = Amp\File\read(__DIR__."/ids.txt"); 
$numFruits = count(explode("\n",$filex));
if (file_exists(__DIR__."/data/page_var.txt")) {
$pages_varx = Amp\File\read(__DIR__."/data/page_var.txt"); 
$pages_var = $pages_varx; 
$resultpages = ceil($numFruits / 5);

$query->answer($message = "מציג דף $pages_var/$resultpages 📃", $alert = true, $url = null, $cacheTime = 0);
}
}
}

#[Handler]
    public function adnimhandle(callbackQuery $query): void
    {
$userid = $query->userId;  
$querydata = $query->data;  
$msgid = $query->messageId;   
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "null";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "null";
}


$flag = false;
$ADMIN = $this->getAdminIds();
foreach ($ADMIN as $user) {
$usertocheck = (string) $user;
$senderidtocheck = (string) $userid;
if (preg_match('/^' . preg_quote($senderidtocheck, '/') . '\b/i', $usertocheck)) {
$flag = true;
break;
}
}

if($flag){

if(preg_match('/openprofile_/',$querydata)){ 
$str = str_replace('openprofile_','',$querydata);

try {
	
$User_Full2 = $this->getInfo($str);
$chackdb = true;
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/This peer is not present in the internal peer database/",$estring)){
$chackdb = false;
}
}

$type = $User_Full2['type']?? null;
if($type == null){
$type = "(null)";
}

if($type == "channel" || "supergroup" || "chat"){
$first_name2 = $User_Full2['Chat']['title']?? null;
if($first_name2 == null){
$first_name2 = "(null)";
}
$username2 = "(null)";
}

if($type == "user" || "bot"){
$first_name2 = $User_Full2['User']['first_name']?? null;
if($first_name2 == null){
$first_name2 = "(null)";
}

try {
$usernames = $User_Full2['User']['usernames']?? null;
$newLangsCommausername = null;
$peerList2username = [];
foreach ($usernames as $username) {
$usernamexfr = $username['username'];
$usernamexfr = "@".$usernamexfr;
$peerList2username[]=$usernamexfr;
}
$newLangsCommausername = implode(" ", $peerList2username);
}catch (\danog\MadelineProto\Exception $e) {
} catch (\danog\MadelineProto\RPCErrorException $e) {
}
$username2 = $User_Full2['User']['username']?? null;
if($username2 == null){	
if($newLangsCommausername != null){
$username2 = $newLangsCommausername;
}else{
$username2 = "(null)";
}
}else{
$username2 = "@".$username2;
}

}

try {
$this->messages->deleteMessages(revoke: true, id: [$msgid]); 

}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

$sentMessage = $this->messages->sendMessage(peer: $userid, message: "⏳");
$sentMessage2 = $this->extractMessageId($sentMessage);

try {

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל"],];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

if($chackdb != false){
$txtuserag = "
════════════
█│║║▌│║║█║│║║█║
█─ 🆔  <a href='mention:$str'>$str </a>
█─ 🎭  $username2
█─ 👤  $first_name2
█─ 🔌  $type";
}
if($chackdb != true){
$txtuserag = "
════════════
█│║║▌│║║█║│║║█║
█─ 🆔  $str
█─ 🎭  $username2
█─ 👤  $first_name2
█─ 🔌  $type";
}

$this->messages->editMessage(peer: $userid, id: $sentMessage2, message: "$txtuserag", reply_markup: $bot_API_markup, parse_mode: 'HTML');

}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/This peer is not present in the internal peer database/",$estring)){
}
if(preg_match("/Undefined array key/",$estring)){
}
}

if (file_exists(__DIR__."/data/$userid/grs1.txt")) {
unlink(__DIR__."/data/$userid/grs1.txt");
}

}

}
}
	
    #[Handler]
    public function handlemashovMessageAdmin(Incoming & PrivateMessage & FromAdmin $message): void
    {
$messagetext = $message->message;
$messageid = $message->id;
$senderid = $message->senderId;
$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "null";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "null";
}

if(!preg_match('/^\/([Ss]tart)/',$messagetext)){  
    if (file_exists(__DIR__."/data/$senderid/grs1.txt")) {
$grs1 = Amp\File\read(__DIR__."/data/$senderid/grs1.txt");    
if($grs1 == "mashovadminmsg"){
unlink(__DIR__."/data/$senderid/grs1.txt");	

    if (file_exists(__DIR__."/data/$senderid/usertosend.txt")) {
$peer = Amp\File\read(__DIR__."/data/$senderid/usertosend.txt");   
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"openprofile2_$peer"],];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
try {
	
$User_Full2 = $this->getInfo($peer);
$chackdb = true;
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/This peer is not present in the internal peer database/",$estring)){
$chackdb = false;
}
}
$first_name2 = $User_Full2['User']['first_name']?? null;
if($first_name2 == null){
$first_name2 = "(null)";
}
$last_name2 = $User_Full2['User']['last_name']?? null;
if($last_name2 == null){
$last_name2 = "(null)";
}
$username2 = $User_Full2['User']['username']?? null;
if($username2 == null){
$username2 = "(null)";
}


 if (file_exists(__DIR__."/data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read(__DIR__."/data/$senderid/messagetodelete.txt");  
try {

if($chackdb != false){
$this->messages->editMessage(peer: $senderid, id: $filexmsgid, message: "ההודעה נשלחה ל<a href='mention:$peer'>$first_name2</a>", reply_markup: $bot_API_markup, parse_mode: 'HTML');
}
if($chackdb != true){
$this->messages->editMessage(peer: $senderid, id: $filexmsgid, message: "ההודעה נשלחה ל $peer", reply_markup: $bot_API_markup, parse_mode: 'HTML');
}
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}else{
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}else{
}
}

//unlink("data/$senderid/messagetodelete.txt");
}

try {
$Updates = $this->messages->forwardMessages(silent: false, background: true, drop_author: true, drop_media_captions: false, noforwards: true,id: [$messageid], to_peer: "$peer");
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}else{
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}else{
}
}

try {
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}else{
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}else{
}
}

    }
    }


    }






    }
    }

#[FilterButtonQueryData('שידורלמשתמשים')] 
public function addsoheshidur1(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup[] = [['text'=>"מחק הודעה אחרונה 🗑",'callback_data'=>"בקרוב"]];
$bot_API_markup[] = [['text'=>"שלח הודעה למנויים 📮",'callback_data'=>"שידורלמשתמשים2"]];
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהמנהל2"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>תפריט שידור, אנא בחר:</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('שידורלמשתמשים2')] 
public function addsoheshidur12(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup[] = [['text'=>"❌ ביטול ❌",'callback_data'=>"חזרהמנהל2"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>נא שלח את ההודעה שתרצה לשלוח:</b>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

Amp\File\write(__DIR__."/data/$userid/grs1.txt", 'broadcast1');
$msgqutryid = $query->messageId;
Amp\File\write(__DIR__."/data/$userid/messagetodelete.txt", "$msgqutryid");
}

    #[Handler]
    public function handlebroadcast1(Incoming & PrivateMessage & FromAdmin $message): void
    {
$messagetext = $message->message;
$messageid = $message->id;
$messagefile = $message->media;
$grouped_id = $message->groupedId;
$entities = $message->entities;
$senderid = $message->senderId;
$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "null";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "null";
}



    if (file_exists(__DIR__."/data/$senderid/grs1.txt")) {
$check = Amp\File\read(__DIR__."/data/$senderid/grs1.txt");    
if($check == "broadcast1"){
    
if(!preg_match('/^\/([Ss]tart)/',$messagetext)){   

$messageLength = mb_strlen($messagetext);

if($messageLength > 1024) {
	
			try {
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}
$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"❌ ביטול ❌",'callback_data'=>"חזרהמנהל2"]
        ]
    ]
];

$sentMessage = $this->messages->sendMessage(peer: $message->senderId, message: "נא לשלוח כיתוב עד 1024 תווים בלבד.
כמות התווים ששלחת: $messageLength", reply_markup: $bot_API_markup);


 if (file_exists(__DIR__."/data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read(__DIR__."/data/$senderid/messagetodelete.txt");  

			try {
$this->messages->deleteMessages(revoke: true, id: [$filexmsgid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

unlink(__DIR__."/data/$senderid/messagetodelete.txt");
}

}else{
unlink(__DIR__."/data/$senderid/grs1.txt"); 

if($messagetext != null){
Amp\File\write(__DIR__."/data/$senderid/txt.txt", "$messagetext");
Amp\File\write(__DIR__."/data/$senderid/ent.txt", json_encode(array_map(static fn($e) => $e->toMTProto(),$entities,)));	
}
if(!$messagefile){
}else{
$botApiFileId = $message->media->botApiFileId;
Amp\File\write(__DIR__."/data/$senderid/media.txt", "$botApiFileId");
}

			try {
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

 if (file_exists(__DIR__."/data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read(__DIR__."/data/$senderid/messagetodelete.txt");  

			try {
$this->messages->deleteMessages(revoke: true, id: [$filexmsgid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

unlink(__DIR__."/data/$senderid/messagetodelete.txt");
}


if (file_exists(__DIR__."/data/broadcastsend.txt")) {
$broadcast_send = Amp\File\read(__DIR__."/data/broadcastsend.txt");
}
if (!file_exists(__DIR__."/data/broadcastsend.txt")) {
$broadcast_send = "כולם";
}


if (file_exists(__DIR__."/data/pinmessage.txt")) {
$bot_API_markup[] = [['text'=>"📌 נעץ הודעה בצ'אט: ✔️",'callback_data'=>"נעץהודעהללא"]];
}
if (!file_exists(__DIR__."/data/pinmessage.txt")) {
$bot_API_markup[] = [['text'=>"📌 נעץ הודעה בצ'אט: ✖️",'callback_data'=>"נעץהודעה"]];
}

$bot_API_markup[] = [['text'=>"📮 יעד תפוצה: $broadcast_send",'callback_data'=>"מצבתפוצה"]];

$bot_API_markup[] = [['text'=>"👁 הצג כפתורי קישור",'callback_data'=>"צפהבכפתורים"]];
$bot_API_markup[] = [['text'=>"🔌 הוסף כפתורי קישור ➕",'callback_data'=>"הוספתכפתורים"]];

$bot_API_markup[] = [['text'=>"✅ שדר הודעה ✅",'callback_data'=>"שדרהודעה"]];

$bot_API_markup[] = [['text'=>"❌ ביטול ❌",'callback_data'=>"חזרהמנהל2"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

 if (file_exists(__DIR__."/data/$senderid/txt.txt")) {
$filexmsgidtxt = Amp\File\read(__DIR__."/data/$senderid/txt.txt");  
}else{
$filexmsgidtxt = null; 
}
  if (file_exists(__DIR__."/data/$senderid/ent.txt")) {
$filexmsgident = json_decode(Amp\File\read(__DIR__."/data/$senderid/ent.txt"),true);  
  }else{
$filexmsgident = null;  
  }	  
  if (file_exists(__DIR__."/data/$senderid/media.txt")) {
$filexmsgidmedia = Amp\File\read(__DIR__."/data/$senderid/media.txt");  
  }else{
$filexmsgidmedia = null;  
  }	 

if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){
$sentMessage = $this->messages->sendMedia(peer: $message->senderId, message: "$filexmsgidtxt", entities: $filexmsgident, media: $filexmsgidmedia, reply_markup: $bot_API_markup);
}else{
$sentMessage = $this->messages->sendMedia(peer: $message->senderId, media: $filexmsgidmedia, reply_markup: $bot_API_markup);
}

}else{

if($filexmsgidtxt != null){
$sentMessage = $this->messages->sendMessage(peer: $message->senderId, message: "$filexmsgidtxt", entities: $filexmsgident, reply_markup: $bot_API_markup);
}
}

}


	
}


}

}
	}

#[FilterButtonQueryData('חזרהתפריטשידור')] 
public function hazarashidur(callbackQuery $query)
{
$userid = $query->userId; 
$msgqutryid = $query->messageId;   
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists(__DIR__."/data/broadcastsend.txt")) {
$broadcast_send = Amp\File\read(__DIR__."/data/broadcastsend.txt");
}
if (!file_exists(__DIR__."/data/broadcastsend.txt")) {
$broadcast_send = "כולם";
}


if (file_exists(__DIR__."/data/pinmessage.txt")) {
$bot_API_markup[] = [['text'=>"📌 נעץ הודעה בצ'אט: ✔️",'callback_data'=>"נעץהודעהללא"]];
}
if (!file_exists(__DIR__."/data/pinmessage.txt")) {
$bot_API_markup[] = [['text'=>"📌 נעץ הודעה בצ'אט: ✖️",'callback_data'=>"נעץהודעה"]];
}
$bot_API_markup[] = [['text'=>"📮 יעד תפוצה: $broadcast_send",'callback_data'=>"מצבתפוצה"]];

$bot_API_markup[] = [['text'=>"👁 הצג כפתורי קישור",'callback_data'=>"צפהבכפתורים"]];
$bot_API_markup[] = [['text'=>"🔌 הוסף כפתורי קישור ➕",'callback_data'=>"הוספתכפתורים"]];

$bot_API_markup[] = [['text'=>"✅ שדר הודעה ✅",'callback_data'=>"שדרהודעה"]];

$bot_API_markup[] = [['text'=>"❌ ביטול ❌",'callback_data'=>"חזרהמנהל2"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

 if (file_exists(__DIR__."/data/$userid/txt.txt")) {
$filexmsgidtxt = Amp\File\read(__DIR__."/data/$userid/txt.txt");  
}else{
$filexmsgidtxt = null; 
}
  if (file_exists(__DIR__."/data/$userid/ent.txt")) {
$filexmsgident = json_decode(Amp\File\read(__DIR__."/data/$userid/ent.txt"),true);  
  }else{
$filexmsgident = null;  
  }	

if($filexmsgidtxt != null){
$this->messages->editMessage(peer: $userid, id: $msgqutryid, message: "$filexmsgidtxt", entities: $filexmsgident, reply_markup: $bot_API_markup);
}else{
$query->editText($message = "תפריט שידור אנא בחר:", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

}

#[FilterButtonQueryData('נעץהודעה')] 
public function addsoheshidur1forneitza1(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

Amp\File\write(__DIR__."/data/pinmessage.txt","on");

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהתפריטשידור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "כעת ההודעה תנעץ בצ'אט ✔️", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('נעץהודעהללא')] 
public function addsoheshidur1forneitza2(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

if (file_exists(__DIR__."/data/pinmessage.txt")) {
unlink(__DIR__."/data/pinmessage.txt");
}

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהתפריטשידור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "כעת ההודעה לא תנעץ בצ'אט ✖️", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('מצבתפוצה')] 
public function broadsetsenders(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup[] = [['text'=>"רק למשתמשים",'callback_data'=>"מצבתפוצה1"]];
$bot_API_markup[] = [['text'=>"רק לערוצים",'callback_data'=>"מצבתפוצה2"]];
$bot_API_markup[] = [['text'=>"רק לקבוצות",'callback_data'=>"מצבתפוצה3"]];
$bot_API_markup[] = [['text'=>"לכל המנויים",'callback_data'=>"מצבתפוצה4"]];
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהתפריטשידור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>אנא בחר מצב תפוצה 🔘</b>
האם לשלוח את ההודעה לכל המנויים או עם פילטר: רק משתמשים/קבוצות/ערוצים.", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('מצבתפוצה1')] 
public function broadsetsenders1(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

Amp\File\write(__DIR__."/data/broadcastsend.txt","משתמשים");

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהתפריטשידור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>התפוצה שנבחרה:</b> רק למשתמשים", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('מצבתפוצה2')] 
public function broadsetsenders2(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

Amp\File\write(__DIR__."/data/broadcastsend.txt","ערוצים");

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהתפריטשידור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>התפוצה שנבחרה:</b> רק לערוצים", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('מצבתפוצה3')] 
public function broadsetsenders3(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

Amp\File\write(__DIR__."/data/broadcastsend.txt","קבוצות");

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהתפריטשידור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>התפוצה שנבחרה:</b> רק לקבוצות", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('מצבתפוצה4')] 
public function broadsetsenders4(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

Amp\File\write(__DIR__."/data/broadcastsend.txt","כולם");

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהתפריטשידור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>התפוצה שנבחרה:</b> כולם", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
}

#[FilterButtonQueryData('הוספתכפתורים')] 
public function hosafkaf(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

$bot_API_markup[] = [['text'=>"❌ ביטול ❌",'callback_data'=>"חזרהתפריטשידור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

$query->editText($message = "<b>שלח את הכפתורים שתרצה להוסיף בפורמט הבא:</b>
<pre>Button text 1 - http://www.example.com/ \nButton text 2 - http://www.example2.com/</pre>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);

Amp\File\write(__DIR__."/data/$userid/grs1.txt", 'addBUTTONS');
$msgqutryid = $query->messageId;
Amp\File\write(__DIR__."/data/$userid/messagetodelete.txt", "$msgqutryid");
}

    #[Handler]
    public function handlebuttons(Incoming & PrivateMessage & FromAdmin $message): void
    {
$messagetext = $message->message;
$entities = $message->entities;
$messagefile = $message->media;
$messageid = $message->id;
$senderid = $message->senderId;
$User_Full = $this->getInfo($message->senderId);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}
$last_name = $User_Full['User']['last_name']?? null;
if($last_name == null){
$last_name = "null";
}
$username = $User_Full['User']['username']?? null;
if($username == null){
$username = "null";
}



    if (file_exists(__DIR__."/data/$senderid/grs1.txt")) {
$edit = Amp\File\read(__DIR__."/data/$senderid/grs1.txt");    
if($edit == "addBUTTONS"){
 
if(!preg_match('/^\/([Ss]tart)/',$messagetext)){   

if(!function_exists("isTextInCorrectFormat")){
function isTextInCorrectFormat($messagetext) {
    // Split the text into lines
    $lines = explode("\n", $messagetext);
    
    // Define the regex pattern for matching each line
    $pattern = "/^[^:]* - (https?:\/\/[^\s]+)$/i";
    
    foreach ($lines as $line) {
        // Trim leading and trailing whitespace
        $line = trim($line);
        
        // Check if the line matches the pattern
        if (!preg_match($pattern, $line)) {
            return false; // Found a line that doesn't match
        }
    }
    
    return true; // All lines match the pattern
}
}


if (isTextInCorrectFormat($messagetext)) {
unlink(__DIR__."/data/$senderid/grs1.txt");

    if (!file_exists(__DIR__."/data/BUTTONS.txt")) {	
Amp\File\write(__DIR__."/data/BUTTONS.txt", "$messagetext");
	}
    if (file_exists(__DIR__."/data/BUTTONS.txt")) {	
unlink(__DIR__."/data/BUTTONS.txt");
Amp\File\write(__DIR__."/data/BUTTONS.txt", "$messagetext");
	}

			try {
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

 if (file_exists(__DIR__."/data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read(__DIR__."/data/$senderid/messagetodelete.txt");  
$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהתפריטשידור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
$Updates = $this->messages->editMessage(peer: $senderid, id: $filexmsgid, message: "<b>הכפתורים נשמרו בהצלחה! ✔️</b>", reply_markup: $bot_API_markup, parse_mode: 'HTML');

}


} else {
			try {
$this->messages->deleteMessages(revoke: true, id: [$messageid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

 if (file_exists(__DIR__."/data/$senderid/messagetodelete.txt")) {
$filexmsgid = Amp\File\read(__DIR__."/data/$senderid/messagetodelete.txt");  
$bot_API_markup[] = [['text'=>"❌ ביטול ❌",'callback_data'=>"חזרהתפריטשידור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

			try {
$Updates = $this->messages->editMessage(peer: $senderid, id: $filexmsgid, message: "<b>נא שלח את הכפתורים שתרצה להוסיף בפורמט הנכון!</b>", reply_markup: $bot_API_markup, parse_mode: 'HTML');
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_NOT_MODIFIED/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_NOT_MODIFIED') {	
}
}


}
}








	
}


}





	
}


}

#[FilterButtonQueryData('צפהבכפתורים')] 
public function buttonsmanageview(callbackQuery $query)
{
$userid = $query->userId;    
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}

    if (file_exists(__DIR__."/data/BUTTONS.txt")) {
$BUTTONS = Amp\File\read(__DIR__."/data/BUTTONS.txt");  

// Given input string
$input = $BUTTONS;
$peerList2 = [];


$pattern = '/(.+?)\s*-\s*(http[^\s]+)/i';

// Use preg_match_all to find all matches in the input string
preg_match_all($pattern, $input, $matches, PREG_SET_ORDER);

$output = [];

foreach ($matches as $index => $match) {
    // $match[1] is the button text and $match[2] is the URL
    $buttonText = trim($match[1]);
    $buttonUrl = trim($match[2]);

    // Format output
  //  $output["Button" . ($index + 1) . "txt"] = $buttonText;
   // $output["Button" . ($index + 1) . "url"] = $buttonUrl;
	$output[] = "$buttonText - $buttonUrl";
$bot_API_markup[] = [['text'=>"$buttonText",'url'=>"$buttonUrl"]];
}

// Print the output in the desired format
foreach ($output as $key) {
$peerList2[]="$key";
}

$newLangsComma = implode("\n", $peerList2);

$bot_API_markup[] = [['text'=>"חזרה",'callback_data'=>"חזרהתפריטשידור"]];
$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];

if($newLangsComma != null){
$query->editText($message = "<code>$newLangsComma</code>", $replyMarkup = $bot_API_markup, ParseMode::HTML, $noWebpage = false, $scheduleDate = NULL);
	}else{
$BUTTONS = "לא הוגדרו עדיין כפתורים..";  
$query->answer($message = "$BUTTONS", $alert = true, $url = null, $cacheTime = 0);	
	}
	}
	
    if (!file_exists(__DIR__."/data/BUTTONS.txt")) {	
$BUTTONS = "לא הוגדרו עדיין כפתורים..";  
$query->answer($message = "$BUTTONS", $alert = true, $url = null, $cacheTime = 10);
	}
	

}

#[FilterButtonQueryData('שדרהודעה')] 
public function buttonsmanageview2(callbackQuery $query)
{
$userid = $query->userId;  
$msgqutryid = $query->messageId;   
$User_Full = $this->getInfo($userid);
$first_name = $User_Full['User']['first_name']?? null;
if($first_name == null){
$first_name = "null";
}


$sentMessage = $this->messages->sendMessage(peer: $userid, message: "📯 שולח את הודעת השידור..");
$sentMessage2 = $this->extractMessageId($sentMessage);
Amp\File\write(__DIR__."/data/messagetoeditbroadcast1.txt", "$sentMessage2");
Amp\File\write(__DIR__."/data/messagetoeditbroadcast2.txt", "$userid");


    if (file_exists(__DIR__."/data/BUTTONS.txt")) {
$BUTTONS = Amp\File\read(__DIR__."/data/BUTTONS.txt");  
$input = $BUTTONS;

$pattern = '/(.+?)\s*-\s*(http[^\s]+)/i';
preg_match_all($pattern, $input, $matches, PREG_SET_ORDER);
$output = [];
foreach ($matches as $index => $match) {
    $buttonText = trim($match[1]);
    $buttonUrl = trim($match[2]);

	$output[] = "$buttonText - $buttonUrl";
$bot_API_markup[] = [['text'=>"$buttonText",'url'=>"$buttonUrl"]];
}

$bot_API_markup = [ 'inline_keyboard'=> $bot_API_markup,];
}
if (!file_exists(__DIR__."/data/BUTTONS.txt")) {
$bot_API_markup = null;
}

			try {
$this->messages->deleteMessages(revoke: true, id: [$msgqutryid]); 
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_DELETE_FORBIDDEN/",$estring)){
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_DELETE_FORBIDDEN') {	
}
}

 if (file_exists(__DIR__."/data/$userid/txt.txt")) {
$filexmsgidtxt = Amp\File\read(__DIR__."/data/$userid/txt.txt");  
}else{
$filexmsgidtxt = null; 
}
  if (file_exists(__DIR__."/data/$userid/ent.txt")) {
$filexmsgident = json_decode(Amp\File\read(__DIR__."/data/$userid/ent.txt"),true);  
  }else{
$filexmsgident = null;  
  }	  
  if (file_exists(__DIR__."/data/$userid/media.txt")) {
$filexmsgidmedia = Amp\File\read(__DIR__."/data/$userid/media.txt");  
  }else{
$filexmsgidmedia = null;  
  }	 


    if (file_exists(__DIR__."/data/broadcastsend.txt")) {
$check2 = Amp\File\read(__DIR__."/data/broadcastsend.txt");    
    if (file_exists(__DIR__."/data/pinmessage.txt")) {
if($check2 == "משתמשים"){


if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: false,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$this->broadcastMessages(
messages: [['media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: false,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: false,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}
if($check2 == "ערוצים"){

if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: false,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$this->broadcastMessages(
messages: [['media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: false,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: false,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}
if($check2 == "קבוצות"){


if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: true,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$this->broadcastMessages(
messages: [['media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: true,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: true,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}
if($check2 == "כולם"){


if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$this->broadcastMessages(
messages: [['media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}	

}
    if (!file_exists(__DIR__."/data/pinmessage.txt")) {
if($check2 == "משתמשים"){


if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: false,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$this->broadcastMessages(
messages: [['media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: false,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: false,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}
if($check2 == "ערוצים"){


if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: false,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$this->broadcastMessages(
messages: [['media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: false,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: false,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}
if($check2 == "קבוצות"){


if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: true,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$this->broadcastMessages(
messages: [['media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: true,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: false,
        allowBots: true,
        allowGroups: true,
        allowChannels: false,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}
if($check2 == "כולם"){

if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$this->broadcastMessages(
messages: [['media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}	
}





}

    if (!file_exists(__DIR__."/data/broadcastsend.txt")) {

    if (file_exists(__DIR__."/data/pinmessage.txt")) {

if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$this->broadcastMessages(
messages: [['media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'reply_markup' => $bot_API_markup]],
            pin: true,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}
    if (!file_exists(__DIR__."/data/pinmessage.txt")) {


if($filexmsgidmedia != null){
	
if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);


}else{
$this->broadcastMessages(
messages: [['media' => $filexmsgidmedia, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);
}

}else{

if($filexmsgidtxt != null){

$this->broadcastMessages(
messages: [['message' => "$filexmsgidtxt", 'entities' => $filexmsgident, 'reply_markup' => $bot_API_markup]],
            pin: false,
            filter: new Filter(
        allowUsers: true,
        allowBots: true,
        allowGroups: true,
        allowChannels: true,
        blacklist: [], 
        whitelist: null 
)
);

}
}

}


}


}

private int $lastLog = 0;
    #[Handler]
    public function handleBroadcastProgress(Progress $progress): void
    {
                if (time() - $this->lastLog > 5 || $progress->status === Status::GATHERING_PEERS) {
            $this->lastLog = time();


$progressStr = (string) $progress;

 if (file_exists(__DIR__."/data/messagetoeditbroadcast2.txt")) {
$filexmsgid1 = Amp\File\read(__DIR__."/data/messagetoeditbroadcast2.txt");  

 if (file_exists(__DIR__."/data/messagetoeditbroadcast1.txt")) {
$filexmsgid2 = Amp\File\read(__DIR__."/data/messagetoeditbroadcast1.txt");  

$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]
        ]
    ]
];


			try {
$this->messages->editMessage(peer: $filexmsgid1, id: $filexmsgid2, message: "📯 שולח את הודעת השידור..
$progressStr", reply_markup: $bot_API_markup);
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_NOT_MODIFIED/",$estring)){
}else{
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_NOT_MODIFIED') {	
}else{
}
}



 }
 }
 
 
				}

        if (time() - $this->lastLog > 5 || $progress->status === Status::FINISHED) {
            $this->lastLog = time();
// $this->sendMessageToAdmins((string) $progress);
if (file_exists(__DIR__."/data/broadcastsend.txt")) {
$broadcast_send = Amp\File\read(__DIR__."/data/broadcastsend.txt");
}
if (!file_exists(__DIR__."/data/broadcastsend.txt")) {
$broadcast_send = "כולם";
}
//$this->sendMessageToAdmins("✅ ההודעה נשלחה ל: $broadcast_send");

$progressStr = (string) $progress;

    $pendingCount = $progress->pendingCount;
    $sucessCount = $progress->successCount;
    $sucessCount2 = $progress->failCount;

 if (file_exists(__DIR__."/data/messagetoeditbroadcast2.txt")) {
$filexmsgid1 = Amp\File\read(__DIR__."/data/messagetoeditbroadcast2.txt");  

 if (file_exists(__DIR__."/data/messagetoeditbroadcast1.txt")) {
$filexmsgid2 = Amp\File\read(__DIR__."/data/messagetoeditbroadcast1.txt");  
$bot_API_markup = ['inline_keyboard' => 
    [
        [
['text'=>"חזרה",'callback_data'=>"חזרהמנהל"]
        ]
    ]
];


			try {
$this->messages->editMessage(peer: $filexmsgid1, id: $filexmsgid2, message: "📯 הודעת השידור נשלחה בהצלחה!
✅ ההודעה נשלחה ל: $sucessCount
⏳ ממתינים לשליחה: $pendingCount
❌ נכשל בעת השליחה: $sucessCount2", reply_markup: $bot_API_markup);
}catch (\danog\MadelineProto\Exception $e) {
$estring = (string) $e;
if(preg_match("/MESSAGE_NOT_MODIFIED/",$estring)){
}else{
}

} catch (\danog\MadelineProto\RPCErrorException $e) {
    if ($e->rpc === 'MESSAGE_NOT_MODIFIED') {	
}else{
}
}



    if (file_exists(__DIR__."/data/BUTTONS.txt")) {
unlink(__DIR__."/data/BUTTONS.txt");  
	}	
 if (file_exists(__DIR__."/data/$filexmsgid1/txt.txt")) {
unlink(__DIR__."/data/$filexmsgid1/txt.txt");  
}
  if (file_exists(__DIR__."/data/$filexmsgid1/ent.txt")) {
unlink(__DIR__."/data/$filexmsgid1/ent.txt");  
  }	  
  if (file_exists(__DIR__."/data/$filexmsgid1/media.txt")) {
unlink(__DIR__."/data/$filexmsgid1/media.txt");  
  }	 


 }
 }









        }
        }

#[FilterButtonQueryData('בקרוב')]
public function comingsoon(callbackQuery $query)
{  
$userid = $query->userId;  
$query->answer($message = "בקרוב מאוד זה יפעל 💡", $alert = true, $url = null, $cacheTime = 0);
}
    

}

if (file_exists(__DIR__."/bot.madeline")) {
$BOT_TOKEN = parse_ini_file('.env')['BOT_TOKEN'];
MyEventHandler::startAndLoopBot(__DIR__.'/bot.madeline', $BOT_TOKEN);
}else{
$API_ID = parse_ini_file('.env')['API_ID'];
$API_HASH = parse_ini_file('.env')['API_HASH'];
$BOT_TOKEN = parse_ini_file('.env')['BOT_TOKEN'];
$settings = new Settings;
$settings->setAppInfo((new \danog\MadelineProto\Settings\AppInfo)->setApiId((int)$API_ID)->setApiHash($API_HASH));
MyEventHandler::startAndLoopBot(__DIR__.'/bot.madeline', $BOT_TOKEN, $settings);
}
