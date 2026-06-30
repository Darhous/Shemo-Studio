# سجل محطات مشروع Shemo Studio

> هذا الملف هو السجل التراكمي لكل محطة تُنفَّذ في المشروع. كل محطة لها جلسة/محادثة منفصلة. لا تُحذف محطات سابقة — يُضاف الجديد فقط في آخر الملف.

---

## المحطة 0 — الأمان والتدقيق (Safety & Audit)

**التاريخ:** 2026-07-01
**الحالة:** ✅ مكتملة
**النوع:** قراءة فقط (Read-Only) — لا تعديل على أي إعداد

### السياق
الموقع القديم في LocalWP كان قد اتمسح بمعرفة المستخدم قبل بداية هذه المحطة (راجع `Shemo-Studio-Clean-Start/APPROVED-DECISIONS.md` — لا قرارات معتمدة غير رقم 1 "Hybrid studio + visible founder"). بدلاً من تدقيق بيئة قديمة، تم إنشاء **موقع LocalWP جديد فاضي** بالإعدادات الموصى بها في الخطة الأصلية:

| الإعداد | القيمة |
|---|---|
| اسم الموقع | Shemo-Studio-Clean-Start |
| الدومين المحلي | http://shemostudio.local (و HTTPS يعمل) |
| WordPress | 7.0 |
| PHP | 8.3.29 |
| Web server | Nginx 1.26.1 |
| Database | MySQL 8.4.0 |
| مسار الموقع محليًا | `C:\Users\ahmed\Local Sites\Shemo-Studio-Clean-Start` |

### 1. النسخ الاحتياطي
- ✅ نسخة قاعدة البيانات: `_backups/db_2026-07-01_phase0.sql` (≈897 KB)
- ✅ نسخة كاملة للملفات (`app/` + `conf/`): `_backups/files_2026-07-01_phase0.zip` (≈30 MB)
- **المكان:** داخل فولدر الموقع نفسه في LocalWP (`..._backups\`) — **مش في الريبو العام عمدًا**، لتجنب رفع بيانات/ملفات ضخمة لمستودع GitHub.

### 2. الجرد الكامل (Read-Only Inventory)

**الثيمات:**
- مفعّل: `twentytwentyfive` (الافتراضي الحالي)
- موجود وغير مفعّل: `twentytwentyfour`، `twentytwentythree`
- لا يوجد ثيم مخصص بعد (`shemo-child` لسه ينتظر المحطة 3)

**الإضافات:** لا توجد أي إضافة مثبّتة إطلاقًا — فولدر `wp-content/plugins` فاضي تمامًا (ولا حتى Akismet/Hello Dolly الافتراضيين).

**المستخدمين:**

| ID | Username | Email | الدور | تاريخ الإنشاء |
|---|---|---|---|---|
| 1 | darhous | dev-email@wpengine.local | Administrator | 2026-06-30 22:25:59 |

مستخدم واحد فقط ✅ (مش "admin" — مطابق لإرشاد الخطة). ⚠️ الإيميل المسجَّل هو placeholder تلقائي من LocalWP، مش إيميل حقيقي — يحتاج تحديث قبل أي مرحلة لاحقة تعتمد على الإيميل (نماذج، إشعارات).

**المحتوى الافتراضي الموجود:**

| ID | العنوان | النوع | الحالة |
|---|---|---|---|
| 1 | Hello world! | post | publish |
| 2 | Sample Page | page | publish |
| 3 | Privacy Policy | page | draft |

تعليق افتراضي واحد موجود. فولدر الرفع (`uploads/2026/06`) موجود وفاضي.

**الإعدادات الحالية مقابل المطلوب بالمحطة 1:**

| الإعداد | الحالة الآن | الإجراء المطلوب لاحقًا |
|---|---|---|
| Site Title | "Shemo Studio" | ✅ مظبوط بالفعل، لا إجراء |
| Tagline | فاضي | ⏳ تعيين "From Sketch to Screen" |
| Timezone | فاضي (gmt_offset = 0) | ⏳ تعيين Africa/Cairo |
| Permalinks | `/%postname%/` | ✅ مظبوط بالفعل ومطابق للموصى به، لا إجراء |
| Discourage search engines | **غير مفعّل (الموقع مفهرس فعليًا)** | ⏳ لازم تتفعّل (بيئة تطوير) |
| Comments / Pingbacks | مفتوحين افتراضيًا | ⏳ لازم تتقفل |
| wp-config: WP_DEBUG | false (معرّف) | — |
| wp-config: WP_DEBUG_LOG | غير معرّف | ⏳ يتضاف ويتفعّل |
| wp-config: WP_DEBUG_DISPLAY | غير معرّف | ⏳ يتضاف ويُعطّل |
| wp-config: SCRIPT_DEBUG | غير معرّف | ⏳ يتضاف ويتفعّل |

### 3. خلاصة الجرد
- **مظبوط بالفعل (لا إجراء):** Site Title، بنية الـ Permalinks.
- **افتراضي يحتاج ضبط بالمحطة 1:** Tagline، Timezone، Discourage search engines، حالة التعليقات، أعلام wp-config.
- **افتراضي يحتاج حذف بالمحطة 1:** Hello World post + تعليقه، Sample Page (ومراجعة هل تُحذف Privacy Policy draft أو تُستخدم لاحقًا).
- **لسه مفيش حاجة منه:** `shemo-child` (ينتظر المحطة 3)، `shemo-core` (ينتظر المحطة 4)، أي إضافة من القائمة الأساسية (تنتظر قفل القرارات 7–14 والتراخيص — بوابة المحطة 2).

### ملاحظة أمان
بيانات دخول الأدمن (يوزر/باسورد) اتفحصت في حدود التحقق من اتصال قاعدة البيانات فقط، ومتسجلتش في أي ملف أو ذاكرة دائمة.

### 4. بوابة المراجعة
المحطة 0 خلصت بالكامل كقراءة فقط، من غير أي تعديل على أي إعداد أو محتوى. **في انتظار موافقة المستخدم للانتقال إلى المحطة 1 (Baseline WordPress Config).**

---

## المحطة 1 — Baseline WordPress Config

**التاريخ:** 2026-07-01
**الحالة:** ✅ مكتملة
**النوع:** تعديل فعلي على إعدادات الموقع (لا إضافات، لا ثيم مخصص بعد)

### السياق
استكمالًا للجرد اللي خرجت بيه المحطة 0، المحطة دي نفّذت كل البنود اللي كانت معلّمة "⏳ يحتاج ضبط/حذف" في جدول المحطة 0، باستخدام WP-CLI 2.12.0 (متاح جاهز جوه بيئة LocalWP) متصل مباشرة بقاعدة بيانات الموقع — من غير أي تعديل يدوي عبر لوحة التحكم.

**أداة التنفيذ:** `wp-cli` عبر PHP 8.3.29 المرفق مع LocalWP، بـ `--path` يشاور على `C:\Users\ahmed\Local Sites\Shemo-Studio-Clean-Start\app\public`.

### 1. إعدادات عامة (Settings → General / Reading / Discussion)

| الإعداد | كان قبل | بقى دلوقتي | طريقة التنفيذ |
|---|---|---|---|
| Site Title | "Shemo Studio" | "Shemo Studio" (بدون تغيير) | — لا إجراء، كان مظبوط بالفعل |
| Tagline | فاضي | **"From Sketch to Screen"** | `wp option update blogdescription` |
| Timezone | فاضي (gmt_offset=0) | **Africa/Cairo** | `wp option update timezone_string` |
| Permalinks | `/%postname%/` | `/%postname%/` (بدون تغيير) | — لا إجراء، كان مظبوط بالفعل |
| Discourage search engines | غير مفعّل (`blog_public=1`) | **مفعّل** (`blog_public=0`) | `wp option update blog_public 0` |
| Default comment status | open | **closed** | `wp option update default_comment_status closed` |
| Default ping status | open | **closed** | `wp option update default_ping_status closed` |

تم التحقق بعد كل تعديل بقراءة القيمة رجوع من قاعدة البيانات (مش بس الأمر اللي اترسل) — كل القيم اتأكدت فعليًا.

### 2. wp-config.php — أعلام تطوير

أُضيفت 3 سطور جديدة بعد تعريف `WP_DEBUG` الحالي (اللي فضل زي ما هو `false`)، قبل سطر `WP_ENVIRONMENT_TYPE`:

```php
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', true );
```

الهدف: أي خطأ/تنبيه PHP يتسجل في ملف لوج (`wp-content/debug.log`) بدل ما يظهر على الشاشة لأي زائر — مناسب لبيئة تطوير محلية بس برضه آمن لو حد فتح الموقع بالخطأ. السطر `WP_DEBUG` نفسه فضل `false` زي ما كان (مفيش داعي يتفعّل دلوقتي، هيتفعّل وقت الحاجة فعلاً للتطوير).

هذا الملف (`wp-config.php`) موجود جوه فولدر موقع LocalWP مش جوه الريبو، فمفيش أي خطورة إنه يترفع على GitHub.

### 3. حذف المحتوى الافتراضي

| العنصر | الإجراء | السبب |
|---|---|---|
| Post #1 "Hello world!" | 🗑️ اتحذف نهائيًا (`wp post delete --force`) | محتوى تجريبي افتراضي، مفيش داعي له |
| Comment #1 (تعليق على Hello World) | 🗑️ اتحذف نهائيًا | كان مرتبط بالبوست المحذوف |
| Page #2 "Sample Page" | 🗑️ اتحذفت نهائيًا (`wp post delete --force`) | محتوى تجريبي افتراضي |
| Page #3 "Privacy Policy" (draft) | ✅ **اتسابت من غير تعديل** | قرار تنفيذي: الصفحة دي draft مش منشورة، ومش بتأثر على وضع الموقع دلوقتي. الأفضل إنها تتسيب كهيكل جاهز (slug `privacy-policy` + الـ ID محجوز) وتتملى بمحتوى حقيقي لاحقًا بدل ما تتحذف وتتعمل من الأول. **محتاجة تأكيد/مراجعة من المستخدم في محطة لاحقة** (مرتبطة بالمحتوى القانوني في الخطة) — لو القرار يتغير، سهل تتحذف وقتها. |

### 4. التحقق النهائي (Verification)

- ✅ `wp core is-installed` → نجح (الاتصال بقاعدة البيانات سليم بعد كل التعديلات)
- ✅ طلب HTTP لـ `http://shemostudio.local/` رجّع **200 OK** من غير أي PHP fatal error
- ✅ الـ `<title>` بقى `Shemo Studio – From Sketch to Screen` (التاجلاين بان فعليًا في الصفحة)
- ✅ ظهر `<meta name="robots" content="noindex, nofollow" />` في الـ `<head>` — تأكيد إن "Discourage search engines" شغّال فعليًا على مستوى الصفحة المعروضة، مش بس في قاعدة البيانات
- ✅ مفيش أي بوست/صفحة متبقّية غير Page #3 (Privacy Policy draft) — لا Hello World ولا Sample Page ولا تعليقات
- ملاحظة: لسه مفيش `wp-content/debug.log` — طبيعي، معناه مفيش أي PHP notice/warning حصل لحد دلوقتي (الملف هيتعمل تلقائي أول ما يحصل حاجة تتسجل)

### 5. حدود المحطة (ما لم يتم لمسه عمدًا)
- مفيش أي ثيم اتغيّر (`twentytwentyfive` لسه مفعّل) — `shemo-child` ينتظر المحطة 3
- مفيش أي إضافة (plugin) اتثبّتت — تنتظر بوابة المحطة 2
- إيميل الأدمن (`dev-email@wpengine.local`) لسه placeholder، متلموسش — لازم يتحدّث قبل أي مرحلة بتعتمد على نماذج/إشعارات

### 6. بوابة المراجعة
المحطة 1 خلصت بالكامل: كل بنود "Baseline WordPress Config" المعلّقة من المحطة 0 اتنفذت وتم التحقق منها فعليًا (مش بس قراءة قيم الإعداد، كمان فحص استجابة الموقع الحقيقية). القرار الوحيد المفتوح هو **مصير صفحة Privacy Policy draft** (راجع جدول البند 3). **في انتظار موافقة المستخدم للانتقال إلى المحطة 2.**

---

## المحطة 2 — بوابة قفل القرارات (Decisions Lock-in Gate)

**التاريخ:** 2026-07-01
**الحالة:** ✅ مكتملة
**النوع:** قرارات فقط — لا تعديل تقني على الموقع، لا تثبيت إضافات

### السياق
بناءً على `APPROVED-DECISIONS.md` (القرار رقم 1 فقط معتمد حتى الآن) و`MASTER-PLAN.md`، المحطة دي مخصصة لاستعراض التوصيات الخاصة بـ**المعمارية التقنية لووردبريس** (البِلدر، الثيم، نمذجة المحتوى، الإضافات الأساسية وتراخيصها) المذكورة في §4 و§8–§10 من الخطة، وعرضها على المستخدم قرار-قرار لأخذ موافقة صريحة قبل تثبيتها في `APPROVED-DECISIONS.md`. **لا قرار يُضاف بدون موافقة صريحة — السكوت لا يُعتبر موافقة.**

جاري الآن عرض القرارات تباعًا على المستخدم عبر أسئلة مباشرة.

### 1. القرارات المعروضة والنتيجة

عُرضت 11 قرارًا (من رقم 2 لرقم 12) واحدًا واحدًا، كل واحد بخياراته وتكلفته/ترخيصه، وانتظرت موافقة صريحة قبل أي إضافة لـ`APPROVED-DECISIONS.md`. لم يُفترض أي قرار من السكوت.

| # | القرار | النتيجة |
|---|---|---|
| 2 | البنية الأساسية (WP 7.0 / PHP 8.3 / Nginx / MySQL) | ✅ اعتماد رسمي لما هو منفّذ بالفعل من المحطة 0 |
| 3 | Page Builder | ✅ **Bricks Builder** (ترخيص مدفوع one-time) |
| 4 | الثيم | ✅ ثيم فرعي مخصص **shemo-child** فوق Bricks |
| 5 | نمذجة المحتوى | ✅ **ACF Pro** (مدفوع) + **shemo-core** (تطوير داخلي) |
| 6 | التعامل مع SVG | ✅ **Safe SVG** (مجاني) |
| 7 | النماذج (Forms) | ✅ **Fluent Forms — النسخة المجانية** (مش Pro، بطلب صريح من المستخدم) |
| 8 | البيع/الدفع | ✅ **SureCart — الباقة المجانية** (Launch، 1.9% رسوم، مش Pro) |
| 9 | SEO | ✅ **Rank Math** (مجاني) |
| 10 | الأمان | ✅ **Wordfence Free** + 2FA + تقوية عامة |
| 11 | ضغط الصور | ✅ **ShortPixel** (البداية بالباقة المجانية، مع ملاحظة ترقية وقت الإطلاق) |
| 12 | أداة الترحيل | ✅ **Duplicator** (مجاني، للاستخدام المستقبلي) |

### 2. نقطة مهمة ظهرت أثناء المحطة — مبدأ "تفضيل المجاني"

أثناء قرار النماذج (7)، عبّر المستخدم عن رغبته في موقع مجاني بالكامل بدل Fluent Forms Pro المدفوعة الموصى بها في الخطة. **متفترضتش إن ده يخص النماذج بس** — رجعت سألت توضيح صريح هل التفضيل عام على كل القرارات المتبقية ولا خاص بالنماذج، فأكّد المستخدم إنه **عام على كل القرارات المتبقية**. على أساس كده اتغيّر قرار 8 (SureCart) لباقة Launch المجانية بدل Pro، وقرار 11 (ShortPixel) للباقة المجانية. القرارات اللي اتعتمدت **قبل** ما يُذكر هذا التفضيل (Bricks Builder وACF Pro، قرارات 3 و5، وهما مدفوعين) **فضلوا زي ما هم بدون مراجعة** — لم يُطلب التراجع عنهم صراحةً، فمتلموسوش.

هذا المبدأ ("عند وجود بديل مجاني عملي، الأفضلية له ما لم يُذكر غير ذلك") اتسجل كملاحظة عامة دائمة في `APPROVED-DECISIONS.md` عشان يوجّه أي قرار إضافة/أداة لاحق.

### 3. ما لم يُغطّى في هذه البوابة (مؤجَّل عمدًا)

- **تفاصيل** تصميم نموذج البريف وسير عملية البيع والأسعار الفعلية (الأداتان نفسهما اتقفلوا، التصميم التفصيلي لأ).
- اللغة (عربي/إنجليزي) وبالتبعية إضافة الترجمة Polylang Pro.
- الهوية البصرية (ألوان، خطوط، لوجو) — خارج نطاق "تقنية ووردبريس" أصلًا.
- إضافات لم تُعرض إطلاقًا في هذه الجلسة: Cloudflare Turnstile (حماية فورم من السبام)، Complianz (كوكيز)، GA4/GTM (تحليلات)، أي أداة CRM/بريدية. **محتاجة بوابة قرار منفصلة قبل تثبيتها**، بنفس أسلوب هذه المحطة — اتسجّلت كفجوة صريحة في `APPROVED-DECISIONS.md` عشان متتنساش.

### 4. حدود المحطة

محطة قرارات فقط — **لا تعديل تقني واحد** على موقع LocalWP، لا تثبيت أي إضافة أو شراء أي ترخيص فعليًا. كل ما تم هو تسجيل التوجه المعتمد في `APPROVED-DECISIONS.md` تمهيدًا للتنفيذ الفعلي في محطات لاحقة (الثيم في محطة 3، تثبيت الإضافات في محطة لاحقة بعدها).

### 5. بوابة المراجعة
المحطة 2 خلصت: 11 قرار معماري/تقني لووردبريس اتقفلوا بموافقة صريحة قرار-بقرار، ومبدأ "تفضيل المجاني" اتسجّل كملاحظة دائمة. الفجوات المتبقية (تفاصيل النماذج/البيع، اللغة، الهوية البصرية، إضافات التحليلات/الكوكيز/مكافحة السبام) موثّقة صراحة كمؤجّلة وليست منسية. **في انتظار موافقة المستخدم للانتقال إلى المحطة 3 (تركيب shemo-child theme).**

---

## المحطة 3 — تركيب ثيم shemo-child الفرعي

**التاريخ:** 2026-07-01
**الحالة:** ✅ مكتملة
**النوع:** تعديل فعلي على موقع LocalWP — تثبيت ثيم/إضافة + تنفيذ Git tracking لأول مرة

### السياق
المحطة دي مخصصة لتنفيذ القرار رقم 4 المعتمد (ثيم فرعي مخصص `shemo-child`)، اعتمادًا على القرار رقم 3 (page builder). قبل أي خطوة تقنية، اتسأل المستخدم صراحةً: هل ترخيص Bricks Builder متاح بالفعل ولا لسه محتاج شراء؟ — بما إن Bricks **مالوش نسخة مجانية إطلاقًا** (ترخيص one-time من $79). ردّ المستخدم إنه **مش هيشتري حاجة، عاوز كل حاجة مجانية**.

### 1. مراجعة وتعديل القرارات المعتمدة (قبل أي تنفيذ)
ده تعارض مباشر مع القرار رقم 3 المُقفَل فعليًا في المحطة 2. اتسأل المستخدم تحديدًا: يرجع عن Bricks لبديل مجاني، يوقف المحطة، ولا يشتري لاحقًا بس يستنى؟ اختار المستخدم صراحةً: **الرجوع عن Bricks → GeneratePress (مجاني)**، وهو البديل (Runner-up) الموثّق أصلاً في `MASTER-PLAN.md` §8.

`APPROVED-DECISIONS.md` اتعدّل قبل أي تنفيذ تقني:
- **القرار 3 الأصلي (Bricks):** بقى `⛔ Superseded`، مع توثيق سبب وتاريخ التراجع، بدون حذف السجل التاريخي.
- **قرار 3 (مُعدَّل) جديد:** ✅ **GeneratePress (مجاني) + GenerateBlocks (مجاني)** — معتمد.
- **القرار 4 (الثيم):** اتعدّل ليصبح `shemo-child` فوق **GeneratePress** بدل Bricks (استراتيجية الثيم الفرعي نفسها فضلت زي ما هي).
- **ملاحظة "تفضيل المجاني" العامة:** اتحدّثت لتوثّق إن القرار 3 (Bricks) اتراجَع عنه فعليًا بناءً على طلب صريح، وإن **القرار 5 (ACF Pro، لسه مدفوع ولم يُراجَع)** لازم يتعرض على المستخدم صراحةً في بداية محطة `shemo-core`/نمذجة المحتوى قبل أي شراء — مش مفروض يتفترض استمراره تلقائيًا.

### 2. إعداد بيئة التنفيذ (wp-cli)
الموقع القديم لـwp-cli (من محطة 1) معروف إنه يحتاج PowerShell + متغيرات بيئة محددة، لكن المسارات نفسها متسجلتش في وقتها. اتعمل اكتشاف جديد للمسارات الفعلية على الجهاز:
- **wp-cli الثنائي:** `C:\Program Files (x86)\Local\resources\extraResources\bin\wp-cli\win32\wp.bat` (مُجمَّع جوه تطبيق Local نفسه، مش جوه فولدر الموقع).
- **PHP 8.3.29:** `C:\Users\ahmed\AppData\Roaming\Local\lightning-services\php-8.3.29+1\bin\win64`
- **MySQL client 8.4.0:** `C:\Users\ahmed\AppData\Roaming\Local\lightning-services\mysql-8.4.0\bin\win64\bin`
- **PHPRC:** `C:\Users\ahmed\AppData\Roaming\Local\run\<site-run-hash>\conf\php` (فيه `php.ini` الفعلي وقت التشغيل)
- **MYSQL_HOME:** `C:\Users\ahmed\AppData\Roaming\Local\run\<site-run-hash>\conf\mysql` (فيه `my.cnf` بإعدادات الاتصال الفعلية — port 10004، user/pass root/root محليًا فقط)

اتأكد الاتصال بـ`wp core is-installed` و`wp core version` (رجّعت 7.0) قبل أي تعديل.

### 3. تثبيت GeneratePress وGenerateBlocks
- `wp theme install generatepress` — نزل من `downloads.wordpress.org` مباشرة (v3.6.1)، اتثبّت بنجاح. ثيم third-party عادي، **مش متتبّع بـGit** (زي ما المخطط له في §11).
- `wp plugin install generateblocks` (v2.3.0) + `wp plugin activate generateblocks` — إضافة third-party عادية، **مش متتبّعة بـGit**.

### 4. بناء ثيم shemo-child (الثيم المخصص — الوحيد المتتبّع بـGit)
اتعمل جوه الريبو نفسه (`Shemo-Studio-Clean-Start/themes/shemo-child/`) — مش جوه فولدر موقع LocalWP مباشرة — مطابقةً لقاعدة §11 ("Git: version child theme + shemo-core only"):

| الملف | المحتوى |
|---|---|
| `style.css` | هيدر الثيم القياسي بووردبريس مع `Template: generatepress` (لازم يطابق اسم فولدر الثيم الأب)، اسم/وصف الثيم |
| `functions.php` | enqueue صحيح لستايل الأب ثم الابن (`wp_enqueue_style` مع dependency بينهم) — أسلوب GeneratePress الموصى به رسميًا لثيم فرعي |

اتعمل **directory junction** (`New-Item -ItemType Junction`, مش symlink — الـjunction ما بيحتجش صلاحيات Administrator على NTFS) من:
`C:\Users\ahmed\Local Sites\Shemo-Studio-Clean-Start\app\public\wp-content\themes\shemo-child`
لـ:
`C:\Users\ahmed\Desktop\New folder (9)\Shemo-Studio-Clean-Start\themes\shemo-child`

بالشكل ده، أي تعديل لاحق على ملفات الثيم بيتعمل **جوه الريبو مباشرة** وWordPress بيشوفه فورًا من غير نسخ يدوي — والريبو فضل هو مصدر الحقيقة الوحيد للكود.

### 5. `.gitignore` جديد للريبو
اتضاف `.gitignore` في جذر الريبو (أول مرة) يطبّق قاعدة §11 حرفيًا: يستثني `themes/*` و`plugins/*` بالكامل إلا `themes/shemo-child/` و`plugins/shemo-core/` (لمحطة لاحقة)، بالإضافة لاستثناء `wp-config.php`، `.env`، `*.log`، `/wp/`، `/wp-content/uploads/`، `node_modules/`.

### 6. التفعيل والتحقق
- `wp theme activate shemo-child` → نجح، `wp theme list` أكّد: `generatepress` بقى `parent`، `shemo-child` بقى `active`.
- `wp plugin list` أكّد `generateblocks` نشط.
- فحص HTTP حي لـ`http://shemostudio.local/` رجّع **200 OK** بدون أي PHP fatal error، مع:
  - `<title>` لسه "Shemo Studio – From Sketch to Screen" (التاجلاين من محطة 1 لسه شغّال)
  - `<meta name="robots" content="noindex, nofollow">` لسه موجود (إعداد محطة 1 لسه شغّال)
  - `<link>` لستايل `generatepress` **و**`shemo-child` معًا في الـ`<head>` (enqueue شغّال صح)
  - `body class` فيها `wp-theme-generatepress wp-child-theme-shemo-child` (تأكيد إضافي إن الثيم الفرعي شغّال فعليًا فوق الأب الصحيح)

### 7. حدود المحطة (ما لم يتم لمسه عمدًا)
- **مفيش تصميم بصري حقيقي بعد** — الثيم الفرعي حاليًا هيكل فاضي (style.css بس هيدر، مفيش design tokens/ألوان/خطوط) لحد ما تُتخذ قرارات الهوية البصرية (مؤجّلة في `APPROVED-DECISIONS.md`).
- **مفيش أي إضافة تانية اتثبّتت** (Safe SVG، Fluent Forms، SureCart، Rank Math، Wordfence، ShortPixel، Duplicator) — كل دول لسه منتظرين محطة تثبيت إضافات منفصلة.
- **`shemo-core` plugin** (نمذجة المحتوى، Projects CPT) لسه منتظر محطة لاحقة — وهيّ نفس المحطة اللي لازم يتعرض فيها قرار ACF Pro (مدفوع) من جديد على المستخدم قبل أي شراء.
- **GeneratePress وGenerateBlocks لسه على الإعدادات الافتراضية تمامًا** — مفيش أي تخصيص (Site Library، ألوان، تايبوغرافي) اتعمل، ده هيحتاج محطة تصميم لاحقة.
- إيميل الأدمن placeholder لسه متلموسش (من محطة 1، لسه مفتوح).

### 8. بوابة المراجعة
المحطة 3 خلصت: اتسجّل تراجع المستخدم الصريح عن قرار Bricks Builder المدفوع واتعتمد بديل مجاني (GeneratePress + GenerateBlocks) بدل منه في `APPROVED-DECISIONS.md` قبل أي تنفيذ، ثيم `shemo-child` اتبنى فعليًا جوه الريبو وبقى الثيم النشط على الموقع عبر directory junction (Git هو مصدر الحقيقة للكود مش فولدر LocalWP)، و`.gitignore` اتضاف لأول مرة يطبّق قاعدة "child theme + shemo-core بس متتبّعين". اتحقق كل حاجة بفحص HTTP حي للموقع مش بس قراءة قيم. **القرار المفتوح:** ACF Pro (قرار 5) لازم يتعرض من جديد على المستخدم في بداية محطة نمذجة المحتوى. **في انتظار موافقة المستخدم للانتقال إلى المحطة 4.**

---

## المحطة 4 — نمذجة المحتوى (Content Modeling)

**التاريخ:** 2026-07-01
**الحالة:** ✅ مكتملة
**النوع:** تعديل فعلي على موقع LocalWP — تثبيت إضافة + بناء إضافة داخلية + تعديل قرار معتمد

### السياق
بداية المحطة: اتسأل المستخدم صراحةً عن قرار رقم 5 (ACF Pro، مدفوع) قبل أي تنفيذ — نفس أسلوب Bricks بالمحطة 3. المستخدم فوّض القرار ("خد انت القرار الأفضل"). تقرر الانتقال لـ**Meta Box (مجاني)** بدل ACF Pro، لأنه بيوفر مجانًا بالضبط الحقول اللي كانت سبب الحاجة لنسخة Pro من ACF تحديدًا (Repeater/Clone، Group، Gallery، Relationship). `APPROVED-DECISIONS.md` اتعدّل بالتفصيل (قرار 5 الأصلي `⛔ Superseded`، قرار 5 مُعدَّل `✅ Approved`) قبل أي تنفيذ تقني.

### 1. مراجعة وتعديل قرار رقم 5 (قبل أي تنفيذ)
نفس الإجراء بالظبط اللي اتعمل مع Bricks بالمحطة 3: القرار رقم 5 الأصلي (ACF Pro) اتسأل عنه المستخدم صراحةً قبل أي خطوة تقنية. المستخدم رد بتفويض القرار للتنفيذ الأفضل. السبب الفني لاختيار **Meta Box** تحديدًا (مش ACF المجاني العادي): النسخة المجانية من ACF بتفتقد Repeater/Flexible Content/Relationship fields (محجوزين لنسخة Pro فقط) — وهما بالظبط نوع الحقول المطلوبة في §19 (deliverables، credits، results، related projects). Meta Box المجاني بيوفرهم بالكامل عبر خاصية `clone => true` على أي حقل + حقل `post` للعلاقات — يعني قدرة أعلى فعليًا من ACF المجاني، مجانًا بالكامل. القرار اتسجّل في `APPROVED-DECISIONS.md` (قرار 5 الأصلي `⛔ Superseded`، قرار 5 مُعدَّل `✅ Approved`) قبل أي تنفيذ تقني — بنفس قاعدة "لا قرار بدون توثيق قبل التنفيذ".

### 2. تثبيت Meta Box (الإضافة المجانية)
- `wp plugin install meta-box --activate` من `downloads.wordpress.org` مباشرة (v5.12.1) — إضافة third-party عادية، **مش متتبّعة بـGit** (نفس مبدأ GeneratePress/GenerateBlocks بالمحطة 3).

### 3. بناء إضافة `shemo-core` (المتتبّعة بـGit بالكامل)
اتبنت جوه الريبو نفسه (`Shemo-Studio-Clean-Start/plugins/shemo-core/`) — `.gitignore` كان جاهز بالفعل من المحطة 3 يستثني `plugins/*` إلا `plugins/shemo-core/`. هيكل الإضافة:

| الملف | المسؤولية |
|---|---|
| `shemo-core.php` | bootstrap الإضافة + تنبيه إداري لو Meta Box مش مفعّلة |
| `includes/post-types.php` | تسجيل CPT `project` (slug `/work/`, archive `/work/`, REST-enabled) |
| `includes/taxonomies.php` | تسجيل 8 تصنيفات على `project` |
| `includes/fields.php` | تسجيل كل حقول §19 عبر فلتر `rwmb_meta_boxes` التابع لـMeta Box |

**CPT `project`:** `public`، `show_in_rest => true` (يفتح الباب لاستخدام REST/headless لاحقًا لو احتجنا)، أرشيف ورابط دائم على `/work/`، يدعم title/editor/excerpt/thumbnail/revisions.

**8 تصنيفات (Taxonomies):** Service، Project Type، Industry، Platform، Tool (غير هرمي عمدًا — حر زي الوسوم، لأن أدوات زي "Premiere Pro"/"Figma" متعددة وحرة مش شجرة ثابتة)، Content Format، Client Type، Visual Style. **"Featured" مش تصنيف عمدًا** — اتسجّل كحقل boolean منفصل (`shemo_featured`) بالظبط زي ما حدده `MASTER-PLAN.md` §19 ("Featured = field flag, not a taxonomy").

**5 مجموعات حقول (Meta Box field groups)، 22 حقل إجمالًا، كلهم بالكود مش بواجهة إدارة GUI** (تفاديًا لنفس نقطة الرفض الموثقة بالخطة: "CPT-UI + Code Snippets — rejected, bloat"):

1. **Project Details** (10 حقول): client label (select: Named/Confidential/Unnamed/Personal)، client name، short summary، project date، project goal، challenge (WYSIWYG)، creative direction (WYSIWYG)، deliverables (نص متكرر عبر `clone`)، external link، featured (checkbox).
2. **Sketch to Screen** (6 حقول): sketch/before/after — كل واحدة منهم اتقسّمت لحقل صورة + حقل فيديو اختياري منفصلين (مفيش نوع حقل واحد "صورة أو فيديو" في Meta Box المجاني، فده الحل العملي البديل، بنفس البيانات).
3. **Media** (2 حقول): main video (Vimeo، نوع oEmbed) + gallery (صور متعددة).
4. **Results & Credibility** (3 حقول): results (مجموعة metric+value متكررة)، testimonial (quote + اسم + دور + صورة)، credits (مجموعة role+name متكررة).
5. **Related Projects** (1 حقل): علاقة Many-to-Many مع نفس CPT `project` عبر حقل `post` (`select_advanced`، متعدد).

### 4. ربط الإضافة بموقع LocalWP
نفس أسلوب junction بالظبط من المحطة 3: **directory junction** (`New-Item -ItemType Junction`) من:
`C:\Users\ahmed\Local Sites\Shemo-Studio-Clean-Start\app\public\wp-content\plugins\shemo-core`
لـ:
`C:\Users\ahmed\Desktop\New folder (9)\Shemo-Studio-Clean-Start\plugins\shemo-core`
الريبو فضل مصدر الحقيقة الوحيد للكود، وWordPress بيشوف أي تعديل لاحق فورًا.

### 5. التفعيل والتحقق (end-to-end، مش بس قراءة كود)
- `wp plugin activate shemo-core` → نجح بدون أي PHP fatal error.
- `wp post-type list` أكّد ظهور `project` (Projects).
- `wp taxonomy list` أكّد ظهور كل الـ8 تصنيفات مربوطة بـ`project`.
- `wp rewrite flush` نجح (رابط `/work/` شغّال).
- **فحص حقيقي لمحرك الحقول:** عبر `wp eval-file` اتأكد إن Meta Box محمّل (`RWMB_Loader` موجودة) وإن فلتر `rwmb_meta_boxes` بيرجّع فعليًا **5 مجموعات بـ22 حقل إجمالًا** بدون أي خطأ PHP.
- **فحص بيانات حقيقي:** اتعمل بوستين تجريبيين من نوع `project`، اتسجّلت عليهم قيم حقول فعلية (client label/name، short summary، featured) وتصنيفات فعلية (service، industry)، واتقرت رجوع من قاعدة البيانات بنجاح — يعني تخزين الحقول والتصنيفات شغّال end-to-end مش بس التسجيل بالكود.
- **فحص HTTP حي:** أرشيف `/work/` رجّع **200 OK** وعرض فعليًا عناوين البوستات التجريبية (تأكيد إن routing/archive شغّال على الموقع الحقيقي مش بس في قاعدة البيانات). الصفحة الرئيسية كمان فحصت ورجّعت **200 OK** مع تأكيد بقاء كل إعدادات المحطات 1 و3 شغّالة (تاجلاين، robots noindex، شيمو تشايلد فوق جينيريت برس) — مفيش أي regression.
- **تنظيف:** البوستين التجريبيين والتصنيفات التجريبية اتمسحوا بالكامل بعد التحقق، الموقع رجع لحالة فاضية من بيانات تجريبية.

### 6. حدود المحطة (ما لم يتم لمسه عمدًا)
- **مفيش أي محتوى حقيقي اتدخل** — كل اللي حصل تجربة تقنية اتمسحت بعدها. صفحات/بوستات Projects الحقيقية لسه منتظرة محتوى case-study فعلي (مرتبط ببند "Case-study data ×6–10" في `MASTER-PLAN.md`، مؤجّل لمرحلة لاحقة).
- **مفيش تصميم بصري للحقول أو للأرشيف** — أرشيف `/work/` شغّال بقالب GeneratePress الافتراضي تمامًا، مفيش single template مخصص لـ`project` ولا تصميم لصفحة الحقول في لوحة التحكم — ينتظر محطة تصميم لاحقة (Bricks اتلغى، فالتصميم هيكون عبر GenerateBlocks/Site Editor).
- **REST API exposure للحقول المخصصة لسه مش مفعّلة** — الـCPT نفسه `show_in_rest => true` لكن حقول Meta Box متسجلتش بـ`register_post_meta`/`show_in_rest` بعد؛ مش مطلوبة الآن (مفيش استخدام headless حاليًا)، بس سهل تتفعّل لاحقًا لو احتجناها.
- **باقي الإضافات الأساسية لسه منتظرة** (Safe SVG، Fluent Forms، SureCart، Rank Math، Wordfence، ShortPixel، Duplicator) — ولا واحدة فيهم اتثبّتت في المحطة دي، برضه منتظرين محطة تثبيت إضافات منفصلة.
- إيميل الأدمن placeholder لسه متلموسش (من محطة 1، لسه مفتوح).
- إضافات لم تُعرض بعد على المستخدم (Cloudflare Turnstile، Complianz، GA4/GTM، CRM) — لسه برّه نطاق كل المحطات لحد الآن.

### 7. بوابة المراجعة
المحطة 4 خلصت: قرار رقم 5 (ACF Pro) اتراجَع عنه بنفس الأسلوب اللي اتعمل بيه Bricks بالمحطة 3 (سؤال صريح قبل أي تنفيذ، توثيق في `APPROVED-DECISIONS.md` قبل أي كود)، واتحل محله **Meta Box (مجاني)** كمحرك حقول بقدرة أعلى فعليًا من ACF المجاني (Repeater/Group/Gallery/Relationship كلهم مجانًا). إضافة `shemo-core` اتبنت بالكامل بالكود جوه الريبو (CPT + 8 تصنيفات + 22 حقل عبر 5 مجموعات)، اتربطت بالموقع عبر directory junction، واتفعّلت بنجاح. كل حاجة اتحقق منها end-to-end: تسجيل بالكود، تخزين بيانات حقيقي في قاعدة البيانات، وعرض حي عبر HTTP — مش بس قراءة كود ثابت. مفيش regression على أي إعداد من المحطات السابقة. **في انتظار موافقة المستخدم للانتقال إلى المحطة 5.**
