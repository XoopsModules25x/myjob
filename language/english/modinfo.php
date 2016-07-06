<?php
//  ------------------------------------------------------------------------ //
//                      MYJOB - MODULE FOR XOOPS 2.0.x                       //
//                  Copyright (c) 2005-2006 Instant Zero                     //
//                     <http://www.instant-zero.com/>                        //
// ------------------------------------------------------------------------- //
//  This program is NOT free software; you can NOT redistribute it and/or    //
//  modify without my assent.                                                //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed WITHOUT ANY WARRANTY; without even the       //
//  implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. //
//                                                                           //
//  ------------------------------------------------------------------------ //

define('_MI_MYJOB_NAME', 'MyJob');
define('_MI_MYJOB_DESC', 'Module for the management of offers and job applications');

define('_MI_MYJOB_ADMMENU1', 'Jobs');
define('_MI_MYJOB_ADMMENU2', 'Applicants');
define('_MI_MYJOB_ADMMENU3', 'Family Status');
define('_MI_MYJOB_ADMMENU4', 'Position Types');
define('_MI_MYJOB_ADMMENU5', 'Permissions');
define('_MI_MYJOB_ADMMENU6', 'Location');
define('_MI_MYJOB_ADMMENU7', 'Industries');
define('_MI_MYJOB_ADMMENU8', 'Salary');
define('_MI_MYJOB_ADMMENU9', 'Purge');
define('_MI_MYJOB_ADMMENU10', 'Fields');
define('_MI_MYJOB_ADMMENU11', 'Experience');
define('_MI_MYJOB_INDEX', 'Index');

define('_MI_MYJOB_MENU1', 'My Jobs');
define('_MI_MYJOB_MENU2', 'View requests');
define('_MI_MYJOB_MENU3', 'Submit an offer');
define('_MI_MYJOB_MENU4', 'Send request');
define('_MI_MYJOB_MENU5', 'My Requests');
define('_MI_MYJOB_MENU6', 'My Offers');
define('_MI_MYJOB_MENU7', 'Cart');
define('_MI_MYJOB_MENU8', 'View applications on a map');

define('_MI_MYJOB_OPT0', 'Enable the Jobs?');
define('_MI_MYJOB_OPT0_DSC', 'If you enable this option, users then have the possibility to enter and view jobs');
define('_MI_MYJOB_OPT0B', 'Enable job applications?');
define('_MI_MYJOB_OPT0B_DSC', 'If you enable this option, users then have the possibility to enter and view job applications');

define('_MI_MYJOB_OPT1', 'Default duration of publishing of request & offer');
define('_MI_MYJOB_OPT1_DSC', 'Choose the default duration (in days) of publication of vacancies and job applications');
define('_MI_MYJOB_OPT2', 'Automatic Approval of jobs');
define('_MI_MYJOB_OPT2_DSC', 'Choose whether Jobs should be automatically approved or not');
define('_MI_MYJOB_OPT3', 'Automatic Approval job applications');
define('_MI_MYJOB_OPT3_DSC', 'Choose if employment applications must be approved automatically or not');

define('_MI_MYJOB_OPT4', 'Number of jobs per page');
define('_MI_MYJOB_OPT4_DSC', 'Choose the number of vacancies visible per page to the user (this limit will also serve to RSS and Atom)');

define('_MI_MYJOB_OPT5', 'Number of applications per page');
define('_MI_MYJOB_OPT5_DSC', 'Choose the number of job applications visible page to user (this limit will also serve to RSS and Atom)');

define('_MI_MYJOB_OPT6', 'Text to display in submitting an offer or an application for employment');
define('_MI_MYJOB_OPT6_DSC', 'This text displays legal notices relating to the CNIL for example.');
define('_MI_MYJOB_OPT6_DEFVAL', 'Under the French Data Protection Act of 6 January 1978, you have also the right to access, rectify, modify and delete data concerning you. You can exercise this right by sending an email to the manager of this site. ');

define('_MI_MYJOB_OPT7', 'Use RSS and Atom?');
define('_MI_MYJOB_OPT7_DSC', 'If you use this option, the last offers and job applications  will be accessible via RSS and Atom feeds RSS and Atom job offers will only be available if you have selected. using the jobs, same for applications. ');

define('_MI_MYJOB_OPT8', 'Allow downloading of files with job offers?');
define('_MI_MYJOB_OPT8_DSC', 'Allows people who offer jobs to attach documents.');

define('_MI_MYJOB_OPT9', 'Allow the downloading files with job applications?');
define('_MI_MYJOB_OPT9_DSC', 'Allows people who submit employment applications to attach documents.');

define('_MI_MYJOB_OPT10', 'Maximum size of attachments in KB (1048576 = 1 MB)');
define('_MI_MYJOB_OPT10_DSC', ' ');

define('_MI_MYJOB_OPT11', 'Text à display during the submission of a job');
define('_MI_MYJOB_OPT11_DSC', 'This text allows for example to display an explanatory note.');
define('_MI_MYJOB_OPT11_DEFVAL', 'Your email address is né necessary because it is usedé e to send you an email allowing you to gé rer this offer (modification, deletion extension) This e-mail address is never dé. voilé e. ');

define('_MI_MYJOB_OPT12', 'Text à display during submitting a job application');
define('_MI_MYJOB_OPT12_DSC', 'This text allows for example to display an explanatory note.');
define('_MI_MYJOB_OPT12_DEFVAL',
       'Your email address is necessary for several reasons First, because we use it to send you an email allowing you to respond to this request (modification, deletion extension) and then because it will be used for companies that are interested in your profile to make contact with you. This email address is never published<br>  In addition, your personal data are not publicly visible. ');

define('_MI_MYJOB_OPT13', 'Allow extra time?');
define('_MI_MYJOB_OPT13_DSC', 'Allows people who submit job offers and job applications to request an extension of publication.');

define('_MI_MYJOB_OPT14', 'Email address to receive a copy of requests for contact');
define('_MI_MYJOB_OPT14_DSC', 'If you enter this zone by a valid email address so friend requests (for job applications) will also be senté es à this address.');

define('_MI_MYJOB_OPT15', 'Allow anonymous requests to make contact?');
define('_MI_MYJOB_OPT15_DSC', 'By default only the groups with rights to see the entire contents of the offers can contact requests unless you enable this option.');

define('_MI_MYJOB_OPT16', 'Size tooltips in dhtml');
define('_MI_MYJOB_OPT16_DSC', 'Links and some of the text will contain a dhtml tooltip (set to 0 for not bubble)');

define('_MI_MYJOB_OPT17', 'Allow export requests vCard?');
define('_MI_MYJOB_OPT17_DSC', 'If you enable this option, people who have the right to see all the information in the job application can also export demand in vCard format');

define('_MI_MYJOB_OPT18', 'Allow export offers vCard?');
define('_MI_MYJOB_OPT18_DSC', 'If you enable this option, it will be possible for visitors to export a job in vCard format');

define('_MI_MYJOB_OPT19', 'Times of search');
define('_MI_MYJOB_OPT19_DSC', 'Enter the search time (in days) separated by semicolons');

define('_MI_MYJOB_OPT20', 'Show a link to the offer or the previous and next request?');
define('_MI_MYJOB_OPT20_DSC', 'By setting this option to Yes, a link to the offer or the previous and next request will be visible');

define('_MI_MYJOB_OPT21', 'Display a table listing the last x offers and demands?');
define('_MI_MYJOB_OPT21_DSC', 'Enter the number of offers and requests to display (0 = no table)');

define('_MI_MYJOB_OPT22', 'Allow viewing PDF?');
define('_MI_MYJOB_OPT22_DSC', 'Choose whether requests and offers are visible to PDF');

define('_MI_MYJOB_OPT23', '[Metagen] - Maximum number of meta keywords generate');
define('_MI_MYJOB_OPT23_DSC', 'Choose the maximum number of keywords that will be generated by the module from the content.');

define('_MI_MYJOB_OPT24', '[Metagen] - Order of keywords');
define('_MI_MYJOB_OPT24_DSC', 'Choose the order of appearance of the keyword');
define('_MI_MYJOB_OPT241', 'Order of appearance in the text');
define('_MI_MYJOB_OPT242', 'Order of word frequency');
define('_MI_MYJOB_OPT243', 'Reverse order of word frequency');

define('_MI_MYJOB_OPT25', '[Metagen] - Blacklist');
define('_MI_MYJOB_OPT25_DSC', 'Enter words (separated by commas) that should not be part of the generated keywords. ');

define('_MI_MYJOB_OPT26', 'Related Information');
define('_MI_MYJOB_OPT26_DSC', 'Select if a dropdown list allowing to have related information to be displayed with each offer and each request');

define('_MI_MYJOB_OPT27', 'Mime types for images');
define('_MI_MYJOB_OPT27_DSC ', ' ');

define('_MI_MYJOB_OPT28', 'Allow syndication research?');
define('_MI_MYJOB_OPT28_DSC', ' ');

define('_MI_MYJOB_OPT29', 'Use Cart?');
define('_MI_MYJOB_OPT29_DSC', 'Do recruiters and people up the search for work can use a Cart?');

define('_MI_MYJOB_OPT30', 'Show jobs on a map?');
define('_MI_MYJOB_OPT30_DSC', 'Via Google Maps');

define('_MI_MYJOB_OPT31', 'See job applications on a map?');
define('_MI_MYJOB_OPT31_DSC', 'Via Google Maps');

define('_MI_MYJOB_OPT32', 'Google Maps API Key');
define('_MI_MYJOB_OPT32_DSC', ' ');

define('_MI_MYJOB_GLOBAL_NOTIFY', 'Global');
define('_MI_MYJOB_GLOBAL_NOTIFYDSC', 'Global Notification Options offers and requests.');
define('_MI_MYJOB_NOTIFY1', 'Jobs');
define('_MI_MYJOB_NOTIFY1_DSC', 'Jobs');

define('_MI_MYJOB_NOTIFY2', 'Job applications');
define('_MI_MYJOB_NOTIFY2_DSC', 'Job applications');

define('_MI_MYJOB_NOTIFY3', 'Offer subject');
define('_MI_MYJOB_NOTIFY3_DSC', 'Offer subject');
define('_MI_MYJOB_NOTIFY3_CAP', 'Offer subject');

define('_MI_MYJOB_NOTIFY4', 'Offer advertisingé e');
define('_MI_MYJOB_NOTIFY4_DSC', 'Offer advertisingé e');
define('_MI_MYJOB_NOTIFY4_CAP', 'Offer advertisingé e');

define('_MI_MYJOB_NOTIFY5', 'Request submitted');
define('_MI_MYJOB_NOTIFY5_DSC', 'Request submitted');
define('_MI_MYJOB_NOTIFY5_CAP', 'Request submitted');

define('_MI_MYJOB_NOTIFY6', 'published Applicationé e');
define('_MI_MYJOB_NOTIFY6_DSC', 'published Applicationé e');
define('_MI_MYJOB_NOTIFY6_CAP', 'published Applicationé e');

define('_MI_MYJOB_NOTIFY_MAIL1', 'A new job has just & ecirc; be subject');
define('_MI_MYJOB_NOTIFY_MAIL2', 'A new job has just & ecirc; be publishedé e');
define('_MI_MYJOB_NOTIFY_MAIL3', 'A new job application has just & ecirc; be subject');
define('_MI_MYJOB_NOTIFY_MAIL4', 'A new job application has just & ecirc; be subject');
define('_MI_MYJOB_BNAME1', 'Recent Job Applications');
define('_MI_MYJOB_BNAME2', 'Most Viewed Employment Applications');
define('_MI_MYJOB_BNAME3', 'Job applications at random');
define('_MI_MYJOB_BNAME4', 'Recent Jobs');
define('_MI_MYJOB_BNAME5', 'Most Viewed Jobs');
define('_MI_MYJOB_BNAME6', 'Jobs randomly');
define('_MI_MYJOB_BNAME7', 'Statistics');
define('_MI_MYJOB_BNAME8', 'Top ... most requested in applications for employment');
define('_MI_MYJOB_BNAME9', 'Top ... most requested in jobs ');

define('_MI_MYJOB_OPT33', 'Number of visible items in the administration?');
define('_MI_MYJOB_OPT33_DSC', 'Choose the number of visible elements in lists and tables');

//Help
define('_MI_MYJOB_DIRNAME', basename(dirname(dirname(__DIR__))));
define('_MI_MYJOB_HELP_HEADER', __DIR__ . '/help/helpheader.html');
define('_MI_MYJOB_BACK_2_ADMIN', 'Back to Administration of ');
define('_MI_MYJOB_HELP_OVERVIEW', 'Overview');

//define('_MI_MYJOB_HELP_DIR', __DIR__);

//help multi-page
define('_MI_MYJOB_HELP1', 'YYYYY');
define('_MI_MYJOB_HELP2', 'YYYYY');
define('_MI_MYJOB_HELP3', 'YYYYY');
define('_MI_MYJOB_HELP4', 'YYYYY');
define('_MI_MYJOB_HELP5', 'YYYYY');
define('_MI_MYJOB_HELP6', 'YYYYY');

