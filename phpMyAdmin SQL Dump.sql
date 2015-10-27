-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Окт 27 2015 г., 19:51
-- Версия сервера: 5.6.17
-- Версия PHP: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `chat`
--

-- --------------------------------------------------------

--
-- Структура таблицы `del_msg_log`
--

CREATE TABLE IF NOT EXISTS `del_msg_log` (
  `dm_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mes_id` int(10) unsigned NOT NULL,
  `action_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dm_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Структура таблицы `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `mes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mes_text` text NOT NULL,
  `mes_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `from_user_id` smallint(5) unsigned NOT NULL,
  `to_user_id` smallint(5) unsigned DEFAULT NULL,
  `room_id` tinyint(3) unsigned DEFAULT NULL,
  `type_id` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=84 ;

--
-- Дамп данных таблицы `message`
--

INSERT INTO `message` (`mes_id`, `mes_text`, `mes_date`, `from_user_id`, `to_user_id`, `room_id`, `type_id`, `read`) VALUES
(1, 'jgjhgjhkgjgjgjhgjhgjg', '2015-10-27 16:37:10', 2, NULL, 1, 1, 0),
(2, '<pre>Преимущества использования AngularJS:\r\nDependency injection в стандартной поставке\r\nВозможности модульного тестирования в стандартной поставке\r\ne2e тесты позволяют легко мокать запросы\r\nДекларативность(использование HTML атрибутов по максимуму)\r\nОтличное open source сообщество\r\nДружелюбность к REST\r\nScopes, bindings и watches</pre>', '2015-10-27 16:37:55', 2, NULL, 1, 1, 0),
(3, 'тестовое сообщение тестовое сообщение тестовое сообщение тестовое сообщение тестовое сообщение тестовое сообщение тестовое сообщение тестовое сообщение тестовое сообщение тестовое сообщение ', '2015-10-27 16:40:34', 2, NULL, 1, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `room`
--

CREATE TABLE IF NOT EXISTS `room` (
  `room_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `room_name` varchar(30) NOT NULL,
  `room_desc` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`room_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `room`
--

INSERT INTO `room` (`room_id`, `room_name`, `room_desc`) VALUES
(1, 'Программисты Angular.js', 'технология модель-вид-контроллер JavaScript'),
(2, 'HTML5 и валидация форм', 'использование встроенного контроля ввода данных'),
(4, 'технология AJAX', 'обмен данными с сервером без перезагрузки страницы');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user_name` varchar(30) NOT NULL,
  `user_login` varchar(20) NOT NULL,
  `user_pass_md5` varchar(32) NOT NULL,
  `use_room_id` tinyint(1) unsigned DEFAULT NULL,
  `switch_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_blocked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_login` (`user_login`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`user_id`, `user_type`, `user_name`, `user_login`, `user_pass_md5`, `use_room_id`, `switch_date`, `user_reg_date`, `user_blocked`) VALUES
(1, 1, 'Администратор', 'admin', '21232f297a57a5a743894a0e4a801fc3', NULL, '2015-10-27 18:50:56', '2015-10-21 14:14:26', 0),
(2, 0, 'Сергей Петрович', 'serpet', '9cd0c661ff79b3a4ee506ce7051e6517', NULL, '2015-10-27 16:42:25', '2015-10-23 14:06:49', 0),
(4, 0, 'человек-паук', 'spiderman', '9f05aa4202e4ce8d6a72511dc735cce9', NULL, '2015-10-25 19:53:11', '2015-10-25 19:14:59', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
