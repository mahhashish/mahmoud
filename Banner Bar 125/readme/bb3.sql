CREATE TABLE IF NOT EXISTS `banners` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `image` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  `alt` varchar(100) NOT NULL,
  `impressions` smallint(7) DEFAULT NULL,
  `clicks` smallint(5) DEFAULT NULL,
  `xClick` int(11) DEFAULT NULL,
  `lastClick` datetime DEFAULT NULL,
  `pause` varchar(3) DEFAULT 'no',
  `creationDate` datetime DEFAULT NULL,
  `timer` varchar(8) DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `expired` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

INSERT INTO `banners` (`id`, `image`, `link`, `alt`, `impressions`, `clicks`, `xClick`, `lastClick`, `pause`, `creationDate`, `timer`, `expires`, `expired`) VALUES
(1, 'http://metataggrabber.com/bb3/admin/upload/hostgator1.gif', 'http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=426546844-125', 'hostgator.com', 11, 3, 0, '2013-12-05 14:51:32', 'no', '2013-11-25 07:10:28', 'Not Set', NULL, 'no'),
(2, 'http://mental-man.com/bannerbar/admin/upload/4.gif', 'http://audiojungle.net/?ref=ianjgough', 'audiojungle.net', 55, 4, 0, '2013-12-08 16:13:16', 'no', '2013-11-25 07:14:08', '1 Day', '2013-11-26 07:40:00', 'yes'),
(3, 'http://metataggrabber.com/bb3/admin/upload/2.gif', 'http://graphicriver.net/?ref=ianjgough', 'graphicriver.net', 16, 1, 0, '2013-12-06 10:00:54', 'yes', '2013-11-25 10:17:52', 'Not Set', NULL, 'no'),
(4, 'http://metataggrabber.com/bb3/admin/upload/3.gif', 'http://www.easyhits4u.com/?ref=ianjgough', 'Traffic Exchange with 420,000+ members', 13, 4, 0, '2013-12-07 09:32:12', 'no', '2013-11-25 10:18:46', 'Not Set', NULL, 'no'),
(5, 'http://metataggrabber.com/bb3/admin/upload/1.jpg', 'http://ianjgough.com/wp-content/demos/bannerbar/pipn/index.php', 'Advertise Here', 13, 2, 0, '2013-12-20 15:31:21', 'no', '2013-11-25 10:19:40', 'Not Set', NULL, 'no'),
(6, 'http://metataggrabber.com/bb3/admin/upload/1penny.gif', 'http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=426546844', 'Penny Ads', 15, 1, 0, '2013-12-05 22:07:11', 'no', '2013-11-25 10:20:28', 'Not Set', NULL, 'no'),
(7, 'http://prodegebanners.sitegrip.com/images/swagbucks-125x125.jpg', 'http://www.swagbucks.com/refer/scophie ', 'Swagbucks', 55, 2, 0, '2013-12-04 21:12:22', 'no', '2013-11-25 10:21:12', 'Not Set', NULL, 'no'),
(8, 'http://metataggrabber.com/bb3/admin/upload/8.jpg', 'http://www.1800banners.com/?ref=7161', '1800 Banners', 55, 2, 0, '2013-12-04 05:45:30', 'no', '2013-11-25 10:22:12', 'Not Set', NULL, 'no'),
(9, 'http://metataggrabber.com/bb3/admin/upload/7.gif', 'https://www.mochimedia.com/r/a64b9d86cd6829ce', 'mochimedia.com', 50, 3, 0, '2013-12-07 18:27:32', 'no', '2013-11-25 10:22:50', 'Not Set', NULL, 'no'),
(10, 'http://metataggrabber.com/bb3/admin/upload/5.gif', 'http://3docean.net/?ref=ianjgough', '3docean.net', 55, 3, 0, '2013-12-05 20:20:04', 'no', '2013-11-25 10:24:41', 'Not Set', NULL, 'no');

CREATE TABLE IF NOT EXISTS `client_banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txn_id` varchar(26) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `alt` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `impressions` int(11) NOT NULL,
  `clicks` int(11) NOT NULL,
  `period` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `expires` datetime NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `txn_id` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(62) NOT NULL,
  `payment_amount` decimal(7,2) NOT NULL,
  `payment_status` varchar(25) NOT NULL,
  `period` varchar(10) NOT NULL,
  `adlink` varchar(150) NOT NULL,
  `imglink` varchar(150) NOT NULL,
  `paypal_email` varchar(62) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `createdtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `paypal` (
  `1month` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `3month` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `6month` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `paypal_email` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `email_subject` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `item_name` varchar(75) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `paypal` (`1month`, `3month`, `6month`, `paypal_email`, `email_subject`, `item_name`) VALUES
('1.00', '2.50', '5.00', 'paypal-facilitator@ianjgough.com', 'New Banner Advert', 'Banner Bar Advert');

CREATE TABLE IF NOT EXISTS `settings` (
  `update_alert` tinyint(1) NOT NULL,
  `email` varchar(30) NOT NULL,
  `keylock` varchar(32) DEFAULT NULL,
  `per_page` tinyint(2) NOT NULL DEFAULT '10',
  `auto_lock` tinyint(1) NOT NULL,
  `failed_logins` tinyint(1) NOT NULL,
  `attempts` tinyint(1) NOT NULL AUTO_INCREMENT,
  `new_window` tinyint(1) NOT NULL,
  `display_count` tinyint(2) DEFAULT '5',
  `client_banners` int(11) NOT NULL DEFAULT '0',
  `cron` tinyint(1) NOT NULL,
  PRIMARY KEY (`attempts`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `settings` (`update_alert`, `email`, `keylock`, `per_page`, `auto_lock`, `failed_logins`, `attempts`, `new_window`, `display_count`, `client_banners`, `cron`) VALUES
(0, 'webmaster@ianjgough.com', 'NULL', 5, 3, 0, 1, 1, 5, 2, 0);