--
-- PostgreSQL database dump
--

-- Dumped from database version 14.15 (Ubuntu 14.15-0ubuntu0.22.04.1)
-- Dumped by pg_dump version 14.15 (Ubuntu 14.15-0ubuntu0.22.04.1)

-- Started on 2024-12-18 03:37:39 MSK

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 221 (class 1255 OID 50398)
-- Name: add_exhibit(character varying, date, character varying, integer, character varying); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.add_exhibit(IN p_name character varying, IN p_date date, IN p_country character varying, IN p_exhibtion_number integer, IN p_pic character varying)
    LANGUAGE plpgsql
    AS $$
begin
insert into exhibits_list(name, date, country, exhibition_number, pic)
values (p_name, p_date, p_country, p_exhibition_number, p_pic);
end;
$$;


ALTER PROCEDURE public.add_exhibit(IN p_name character varying, IN p_date date, IN p_country character varying, IN p_exhibtion_number integer, IN p_pic character varying) OWNER TO postgres;

--
-- TOC entry 225 (class 1255 OID 50402)
-- Name: add_review(text, integer, integer); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.add_review(IN p_content text, IN p_observer_id integer, IN p_exhibition_id integer)
    LANGUAGE plpgsql
    AS $$
begin
insert into reviews(review_content, observer_id, exhibition_id)
values(p_content, p_observer_id, p_exhibition_id);
end;
$$;


ALTER PROCEDURE public.add_review(IN p_content text, IN p_observer_id integer, IN p_exhibition_id integer) OWNER TO postgres;

--
-- TOC entry 222 (class 1255 OID 50399)
-- Name: add_worker(integer, character varying, character varying, character varying, date, date, character varying, character varying); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.add_worker(IN p_museum_id integer, IN p_name character varying, IN p_surname character varying, IN p_patronymic character varying, IN p_employment_date date, IN p_birthday date, IN p_login character varying, IN p_password character varying)
    LANGUAGE plpgsql
    AS $$
begin
insert into workers(museum_id, w_name, w_surname, w_patronymic, 
					employment_date, birthday, w_login, w_password)
values (p_museum_id, p_name, p_surname, p_patronymic, p_employment_date, p_birthday,
		p_login, p_password);
end;
$$;


ALTER PROCEDURE public.add_worker(IN p_museum_id integer, IN p_name character varying, IN p_surname character varying, IN p_patronymic character varying, IN p_employment_date date, IN p_birthday date, IN p_login character varying, IN p_password character varying) OWNER TO postgres;

--
-- TOC entry 223 (class 1255 OID 50400)
-- Name: delete_review(integer); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.delete_review(IN p_review_id integer)
    LANGUAGE plpgsql
    AS $$
begin 
delete from reviews where review_id = p_review_id;
end;
$$;


ALTER PROCEDURE public.delete_review(IN p_review_id integer) OWNER TO postgres;

--
-- TOC entry 217 (class 1255 OID 50385)
-- Name: increment_exhibit_id(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.increment_exhibit_id() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin
new.exhibit_id := (select coalesce(max(exhibit_id), 0) + 1 from exhibits_list);
return new;
end;
$$;


ALTER FUNCTION public.increment_exhibit_id() OWNER TO postgres;

--
-- TOC entry 218 (class 1255 OID 50388)
-- Name: increment_observer_id(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.increment_observer_id() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin
new.observer_id := (select coalesce(max(observer_id), 0) + 1 from exhibit_observers);
return new;
end;
$$;


ALTER FUNCTION public.increment_observer_id() OWNER TO postgres;

--
-- TOC entry 220 (class 1255 OID 50392)
-- Name: increment_review_id(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.increment_review_id() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin
new.review_id := (select coalesce(max(review_id), 0) + 1 from exhibit_review);
return new;
end;
$$;


ALTER FUNCTION public.increment_review_id() OWNER TO postgres;

--
-- TOC entry 219 (class 1255 OID 50390)
-- Name: increment_worker_id(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.increment_worker_id() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin
new.worker_id := (select coalesce(max(worker_id), 0) + 1 from exhibit_workers);
return new;
end;
$$;


ALTER FUNCTION public.increment_worker_id() OWNER TO postgres;

--
-- TOC entry 216 (class 1255 OID 50382)
-- Name: set_review_date(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.set_review_date() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin
new.review_date := current_date;
return new;
end;
$$;


ALTER FUNCTION public.set_review_date() OWNER TO postgres;

--
-- TOC entry 224 (class 1255 OID 50401)
-- Name: update_observer_name(integer, character varying); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.update_observer_name(IN p_observer_id integer, IN p_new_name character varying)
    LANGUAGE plpgsql
    AS $$
begin
update exhibit_observers set o_name = p_new_name where observer_id = p_observer_id;
end;
$$;


ALTER PROCEDURE public.update_observer_name(IN p_observer_id integer, IN p_new_name character varying) OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 214 (class 1259 OID 50327)
-- Name: exhibit_observers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.exhibit_observers (
    observer_id integer NOT NULL,
    date date NOT NULL,
    o_name character varying(100) NOT NULL,
    tarif character varying(100) NOT NULL,
    museum_id integer,
    o_login character varying(100) NOT NULL,
    o_password character varying(100) NOT NULL,
    CONSTRAINT check_login CHECK (((o_login)::text ~ '^[a-zA-Z]+$'::text)),
    CONSTRAINT check_password CHECK (((length((o_password)::text) >= 6) AND ((o_password)::text ~ '^[a-zA-Z0-9!@#$%^&*]+$'::text))),
    CONSTRAINT check_tarif CHECK (((tarif)::text = ANY ((ARRAY['детский'::character varying, 'студенческий'::character varying, 'общий'::character varying, 'пенсионный'::character varying])::text[])))
);


ALTER TABLE public.exhibit_observers OWNER TO postgres;

--
-- TOC entry 212 (class 1259 OID 50315)
-- Name: exhibit_patrons; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.exhibit_patrons (
    patron_id integer NOT NULL,
    exhibit_id integer NOT NULL
);


ALTER TABLE public.exhibit_patrons OWNER TO postgres;

--
-- TOC entry 213 (class 1259 OID 50320)
-- Name: exhibit_review; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.exhibit_review (
    review_content text,
    review_id integer NOT NULL,
    review_date date,
    observer_id integer,
    exhibtion_id integer
);


ALTER TABLE public.exhibit_review OWNER TO postgres;

--
-- TOC entry 211 (class 1259 OID 50308)
-- Name: exhibit_workers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.exhibit_workers (
    worker_id integer NOT NULL,
    museum_id integer,
    w_name character varying(60) NOT NULL,
    w_surname character varying(60) NOT NULL,
    w_patronymic character varying(60) NOT NULL,
    employment_date date NOT NULL,
    birthday date NOT NULL,
    w_login character varying(100) NOT NULL,
    w_password character varying(100) NOT NULL,
    CONSTRAINT check_login CHECK (((w_login)::text ~ '^[a-zA-Z]+$'::text)),
    CONSTRAINT check_password CHECK (((length((w_password)::text) >= 6) AND ((w_password)::text ~ '^[a-zA-Z0-9!@#$%^&*]+$'::text)))
);


ALTER TABLE public.exhibit_workers OWNER TO postgres;

--
-- TOC entry 210 (class 1259 OID 50303)
-- Name: exhibition_list; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.exhibition_list (
    exhibition_name character varying(100) NOT NULL,
    exhibition_number integer NOT NULL,
    museum_id smallint NOT NULL
);


ALTER TABLE public.exhibition_list OWNER TO postgres;

--
-- TOC entry 209 (class 1259 OID 50298)
-- Name: exhibits_list; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.exhibits_list (
    exhibit_id integer NOT NULL,
    name character varying(100) NOT NULL,
    date date NOT NULL,
    country character varying(50) NOT NULL,
    exhibition_number integer,
    pic character varying(100) NOT NULL
);


ALTER TABLE public.exhibits_list OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 50335)
-- Name: museums; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.museums (
    museum_id integer NOT NULL,
    museum_name character varying(100) NOT NULL
);


ALTER TABLE public.museums OWNER TO postgres;

--
-- TOC entry 3419 (class 0 OID 50327)
-- Dependencies: 214
-- Data for Name: exhibit_observers; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.exhibit_observers (observer_id, date, o_name, tarif, museum_id, o_login, o_password) VALUES (1, '2024-01-15', 'Иван', 'общий', 1, 'IvanIvan', '123456');
INSERT INTO public.exhibit_observers (observer_id, date, o_name, tarif, museum_id, o_login, o_password) VALUES (2, '2024-02-18', 'Анна', 'детский', 1, 'AnnaAnna', '123456');
INSERT INTO public.exhibit_observers (observer_id, date, o_name, tarif, museum_id, o_login, o_password) VALUES (3, '2024-03-12', 'Олег', 'студенческий', 1, 'OlegOleg', '123456');
INSERT INTO public.exhibit_observers (observer_id, date, o_name, tarif, museum_id, o_login, o_password) VALUES (4, '2024-03-22', 'Елена', 'общий', 1, 'ElenaElena', '123456');
INSERT INTO public.exhibit_observers (observer_id, date, o_name, tarif, museum_id, o_login, o_password) VALUES (5, '2024-04-05', 'Мария', 'пенсионный', 1, 'MariaMaria', '123456');
INSERT INTO public.exhibit_observers (observer_id, date, o_name, tarif, museum_id, o_login, o_password) VALUES (6, '2024-05-10', 'Сергей', 'общий', 1, 'SergSerg', '123456');
INSERT INTO public.exhibit_observers (observer_id, date, o_name, tarif, museum_id, o_login, o_password) VALUES (7, '2024-06-15', 'Алексей', 'студенческий', 1, 'AlexAlex', '123456');
INSERT INTO public.exhibit_observers (observer_id, date, o_name, tarif, museum_id, o_login, o_password) VALUES (8, '2024-07-20', 'Виктория', 'детский', 1, 'VikaVika', '123456');
INSERT INTO public.exhibit_observers (observer_id, date, o_name, tarif, museum_id, o_login, o_password) VALUES (9, '2024-08-18', 'Дмитрий', 'общий', 1, 'DimaDima', '123456');
INSERT INTO public.exhibit_observers (observer_id, date, o_name, tarif, museum_id, o_login, o_password) VALUES (10, '2024-09-25', 'Татьяна', 'пенсионный', 1, 'TanyaTanya', '123456');


--
-- TOC entry 3417 (class 0 OID 50315)
-- Dependencies: 212
-- Data for Name: exhibit_patrons; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.exhibit_patrons (patron_id, exhibit_id) VALUES (1, 1);
INSERT INTO public.exhibit_patrons (patron_id, exhibit_id) VALUES (2, 2);
INSERT INTO public.exhibit_patrons (patron_id, exhibit_id) VALUES (3, 3);
INSERT INTO public.exhibit_patrons (patron_id, exhibit_id) VALUES (4, 4);
INSERT INTO public.exhibit_patrons (patron_id, exhibit_id) VALUES (5, 5);


--
-- TOC entry 3418 (class 0 OID 50320)
-- Dependencies: 213
-- Data for Name: exhibit_review; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.exhibit_review (review_content, review_id, review_date, observer_id, exhibtion_id) VALUES ('Отличная выставка, особенно понравились чучела животных.', 1, '2024-01-16', 1, 3);
INSERT INTO public.exhibit_review (review_content, review_id, review_date, observer_id, exhibtion_id) VALUES ('Много интересных экспонатов, но хотелось бы больше информации.', 2, '2024-02-19', 2, 1);
INSERT INTO public.exhibit_review (review_content, review_id, review_date, observer_id, exhibtion_id) VALUES ('Восхищён коллекцией старинного оружия!', 3, '2024-03-13', 3, 1);
INSERT INTO public.exhibit_review (review_content, review_id, review_date, observer_id, exhibtion_id) VALUES ('Приятная атмосфера и полезная экскурсия.', 4, '2024-03-23', 4, 2);
INSERT INTO public.exhibit_review (review_content, review_id, review_date, observer_id, exhibtion_id) VALUES ('Зал природы впечатлил разнообразием экспонатов.', 5, '2024-04-06', 5, 3);
INSERT INTO public.exhibit_review (review_content, review_id, review_date, observer_id, exhibtion_id) VALUES ('Понравилась коллекция монет и фарфора.', 6, '2024-05-11', 6, 7);
INSERT INTO public.exhibit_review (review_content, review_id, review_date, observer_id, exhibtion_id) VALUES ('Очень красиво оформлены выставочные залы.', 7, '2024-06-16', 7, 2);
INSERT INTO public.exhibit_review (review_content, review_id, review_date, observer_id, exhibtion_id) VALUES ('Интересные экспозиции, особенно про Толстого понравилось.', 8, '2024-07-21', 8, 10);
INSERT INTO public.exhibit_review (review_content, review_id, review_date, observer_id, exhibtion_id) VALUES ('Дружелюбный персонал и богатая коллекция.', 9, '2024-08-19', 9, 2);
INSERT INTO public.exhibit_review (review_content, review_id, review_date, observer_id, exhibtion_id) VALUES ('Обязательно вернусь снова с друзьями!', 10, '2024-09-26', 10, 2);
INSERT INTO public.exhibit_review (review_content, review_id, review_date, observer_id, exhibtion_id) VALUES ('Классное оружие!', 11, '2024-12-10', 1, 1);


--
-- TOC entry 3416 (class 0 OID 50308)
-- Dependencies: 211
-- Data for Name: exhibit_workers; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.exhibit_workers (worker_id, museum_id, w_name, w_surname, w_patronymic, employment_date, birthday, w_login, w_password) VALUES (1, 1, 'Алексей', 'Иванов', 'Петрович', '2015-03-01', '1985-06-12', 'walex', '123456');
INSERT INTO public.exhibit_workers (worker_id, museum_id, w_name, w_surname, w_patronymic, employment_date, birthday, w_login, w_password) VALUES (2, 1, 'Елена', 'Смирнова', 'Алексеевна', '2016-05-15', '1990-07-18', 'welena', '123456');
INSERT INTO public.exhibit_workers (worker_id, museum_id, w_name, w_surname, w_patronymic, employment_date, birthday, w_login, w_password) VALUES (3, 1, 'Владимир', 'Кузнецов', 'Игоревич', '2018-09-12', '1982-04-05', 'wvladimir', '123456');
INSERT INTO public.exhibit_workers (worker_id, museum_id, w_name, w_surname, w_patronymic, employment_date, birthday, w_login, w_password) VALUES (4, 1, 'Анна', 'Петрова', 'Сергеевна', '2019-11-20', '1992-08-10', 'wanna', '123456');
INSERT INTO public.exhibit_workers (worker_id, museum_id, w_name, w_surname, w_patronymic, employment_date, birthday, w_login, w_password) VALUES (5, 1, 'Игорь', 'Васильев', 'Дмитриевич', '2020-01-10', '1987-03-03', 'wigor', '123456');
INSERT INTO public.exhibit_workers (worker_id, museum_id, w_name, w_surname, w_patronymic, employment_date, birthday, w_login, w_password) VALUES (6, 1, 'Татьяна', 'Морозова', 'Викторовна', '2021-04-15', '1994-09-25', 'wtatyana', '123456');
INSERT INTO public.exhibit_workers (worker_id, museum_id, w_name, w_surname, w_patronymic, employment_date, birthday, w_login, w_password) VALUES (7, 1, 'Сергей', 'Сидоров', 'Павлович', '2022-07-08', '1989-02-17', 'wserg', '123456');
INSERT INTO public.exhibit_workers (worker_id, museum_id, w_name, w_surname, w_patronymic, employment_date, birthday, w_login, w_password) VALUES (8, 1, 'Мария', 'Захарова', 'Евгеньевна', '2023-02-01', '1995-11-11', 'wmaria', '123456');
INSERT INTO public.exhibit_workers (worker_id, museum_id, w_name, w_surname, w_patronymic, employment_date, birthday, w_login, w_password) VALUES (9, 1, 'Дмитрий', 'Орлов', 'Константинович', '2023-06-14', '1988-12-15', 'wdmitr', '123456');
INSERT INTO public.exhibit_workers (worker_id, museum_id, w_name, w_surname, w_patronymic, employment_date, birthday, w_login, w_password) VALUES (10, 1, 'Екатерина', 'Смирнова', 'Игоревна', '2023-09-20', '1993-10-03', 'wkatya', '123456');


--
-- TOC entry 3415 (class 0 OID 50303)
-- Dependencies: 210
-- Data for Name: exhibition_list; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.exhibition_list (exhibition_name, exhibition_number, museum_id) VALUES ('Арсенал', 1, 1);
INSERT INTO public.exhibition_list (exhibition_name, exhibition_number, museum_id) VALUES ('Белый зал', 2, 1);
INSERT INTO public.exhibition_list (exhibition_name, exhibition_number, museum_id) VALUES ('Природа Ивановского края', 3, 1);
INSERT INTO public.exhibition_list (exhibition_name, exhibition_number, museum_id) VALUES ('Мемориальный кабинет Д.Г. Бурылина', 4, 1);
INSERT INTO public.exhibition_list (exhibition_name, exhibition_number, museum_id) VALUES ('Европейская коллекция', 5, 1);
INSERT INTO public.exhibition_list (exhibition_name, exhibition_number, museum_id) VALUES ('Книжный зал', 6, 1);
INSERT INTO public.exhibition_list (exhibition_name, exhibition_number, museum_id) VALUES ('Золотая кладовая', 7, 1);
INSERT INTO public.exhibition_list (exhibition_name, exhibition_number, museum_id) VALUES ('Искусство и время', 8, 1);
INSERT INTO public.exhibition_list (exhibition_name, exhibition_number, museum_id) VALUES ('Библиотека Д.Г. Бурылина', 9, 1);
INSERT INTO public.exhibition_list (exhibition_name, exhibition_number, museum_id) VALUES ('Комната Л.Н. Толстого', 10, 1);


--
-- TOC entry 3414 (class 0 OID 50298)
-- Dependencies: 209
-- Data for Name: exhibits_list; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (1, 'Лось', '1961-01-01', 'Россия', 3, '1.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (2, 'Рысь', '1961-01-01', 'Россия', 3, '2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (3, 'Хорь лесной или чёрный', '1961-01-01', 'Россия', 3, '3_1.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (4, 'Куница лесная', '1961-01-01', 'Россия', 3, '4_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (5, 'Горностай и ласка', '1961-01-01', 'Россия', 3, '5_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (6, 'Барсук', '1961-01-01', 'Россия', 3, '6_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (7, 'Бурый медведь', '1961-01-01', 'Россия', 3, '7_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (8, 'Лисица обыкновенная', '1961-01-01', 'Россия', 3, '8_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (9, 'Волк', '1961-01-01', 'Россия', 3, '9_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (10, 'Кукушка обыкновенная', '1961-01-01', 'Россия', 3, '11.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (11, 'Синица', '1961-01-01', 'Россия', 3, '12.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (12, 'Трёхпалый дятел', '1961-01-01', 'Россия', 3, '13.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (13, 'Воробьиный сыч', '1961-01-01', 'Россия', 3, '14.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (14, 'Ястребиная сова', '1961-01-01', 'Россия', 3, '15.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (15, 'Болотная сова', '1961-01-01', 'Россия', 3, '16.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (16, 'Зелёный дятел', '1961-01-01', 'Россия', 3, '17.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (17, 'Неясыть обыкновенная', '1961-01-01', 'Россия', 3, '18_Неясыть.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (18, 'Лапландская неясыть', '1961-01-01', 'Россия', 3, '19.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (19, 'Филин', '1961-01-01', 'Россия', 3, '20.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (20, 'Глухари /Самка и Самец/', '1961-01-01', 'Россия', 3, '21_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (21, 'Рябчики', '1961-01-01', 'Россия', 3, '22_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (22, 'Белая куропатка осенью', '1961-01-01', 'Россия', 3, '23.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (23, 'Белая куропатка зимой', '1961-01-01', 'Россия', 3, '24.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (24, 'Белая куропатка в летнем наряде', '1961-01-01', 'Россия', 3, '25.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (25, 'Полярная сова', '1961-01-01', 'Россия', 3, '26.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (26, 'Зайчата зайца русака', '1961-01-01', 'Россия', 3, '27_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (27, 'Белка обыкновенная', '1961-01-01', 'Россия', 3, '28_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (28, 'Заяц беляк', '1961-01-01', 'Россия', 3, '29.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (29, 'Зайцы беляки в зимнем наряде', '1961-01-01', 'Россия', 3, '30.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (30, 'Заяц русак', '1961-01-01', 'Россия', 3, '31.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (31, 'Ястреб-тетеревятник', '1961-01-01', 'Россия', 3, '32.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (32, 'Осоед', '1961-01-01', 'Россия', 3, '33.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (33, 'Низинное болото', '1961-01-01', 'Россия', 3, '34_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (34, 'Бобры', '1961-01-01', 'Россия', 3, '35_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (35, 'Норка европейская', '1961-01-01', 'Россия', 3, '36.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (36, 'Ондатра', '1961-01-01', 'Россия', 3, '37.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (37, 'Выхухоль', '1961-01-01', 'Россия', 3, '38.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (38, 'Выдра', '1961-01-01', 'Россия', 3, '39.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (39, 'Енотовидная собака', '1961-01-01', 'Россия', 3, '40_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (40, 'Карта распространения лесов в области', '1961-01-01', 'Россия', 3, '10.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (42, 'Коллекция монет и медалей', '1900-01-01', 'Россия', 4, '41_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (43, 'Коллекция монет и медалей', '1900-01-01', 'Россия, Украина, Азия', 4, '42_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (44, 'Коллекция книг и икон', '1900-01-01', 'Россия', 4, '43_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (45, 'Коллекция книг и икон', '1900-01-01', 'Россия', 4, '43_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (46, 'Диплом Д.Г. Бурылину от Парижской Академии', '1893-01-01', 'Франция', 4, '44_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (47, 'Диплом Д.Г. Бурылина о праве получения медали на коллекцию медалей,
монет и бумажных денег в Чикаго', '1893-01-01', 'США', 4, '45_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (48, 'Грамота о пожаловании Д.Г. Бурылину звания Потомственного Почётного
гражданина', '1902-01-01', 'Россия', 4, '46.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (49, 'Адрес Д.Г. Бурылину от председателя кружка любителей художеств К.
Кривобокова', '1914-12-26', 'Россия', 4, '47.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (50, 'Фигурка скарабея с египетскими иероглифами на основании', '1900-01-01', 'Россия', 4, '48.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (51, 'Портрет Д.Г. Бурылина', '1909-01-01', 'Россия', 4, '49_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (52, 'Гипсовый портрет Д.Г. Бурылина', '1940-01-01', 'Россия', 4, '50_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (53, 'Чертежи поместья Д.Г. Бурылина', '1900-01-01', 'Россия', 4, '51.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (54, 'Портрет Александра Невского', '1945-01-01', 'СССР', 1, '52.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (55, 'Колокол', '1900-01-01', 'Россия', 1, '53.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (56, 'Персидские шлемы и оружия', '1900-01-01', 'Перся', 1, '54_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (57, 'Князь Владимир Красное Солнышко', '1900-01-01', 'Россия', 1, '55_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (58, 'Ручная пищаль', '1500-01-01', 'Россия', 1, '56_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (59, 'Пушка в остроге', '1700-01-01', 'Россия', 1, '58_Пушка в остроге.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (60, 'Инсталляция с пушкой', '1700-01-01', 'Россия', 1, '59_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (61, 'Доспехи воина', '1700-01-01', 'Россия, Персия, Азия', 1, '60_Доспехи воина.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (63, 'Щиты и копья', '1900-01-01', 'Индоперсия, Россия', 1, '62_1.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (64, 'Кавказские оружия', '1900-01-01', 'Кавказ, Персия, Турция, Албания', 1, '63_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (65, 'Европейские оружия', '1800-01-01', 'Германы, Балканы, Россия', 1, '64_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (66, 'Европейские оружия', '1800-01-01', 'Балканы, Персия, Турция, Бельгия', 1, '65_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (67, 'Европейские оружия', '1800-01-01', 'Европа, Австро-Венгрия', 1, '66_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (68, 'Европейские оружия', '1800-01-01', 'Россия, Бельгия', 1, '67_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (69, 'Инсталляция с пушкой', '1700-01-01', 'Россия', 1, '68_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (70, 'Европейские оружия', '1900-01-01', 'Бельгия, Кавказ, Германия, Франция', 1, '70_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (71, 'Европейские оружия', '1800-01-01', 'Россия', 1, '71_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (72, 'Европейские оружия', '1900-01-01', 'Англия, Франция, Бельгия, СССР', 1, '73_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (73, 'Европейские оружия', '1800-01-01', 'Зап. Европа, Фрация, Турция', 1, '74_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (74, 'Европейские оружия', '1800-01-01', 'Россия, Германия, Австро-Венгрия', 1, '75_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (75, 'Европейские оружия', '1900-01-01', 'Россия, Франция', 1, '76_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (76, 'Европейские оружия', '1900-01-01', 'Персия, Кавказ', 1, '77_4.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (77, 'Европейские оружия', '1800-01-01', 'Россия', 1, '78_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (78, 'Европейские оружия', '1800-01-01', 'Россия', 1, '78_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (79, 'Европейские оружия', '1800-01-01', 'Кавказ, Турция, Афганистан', 1, '79_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (80, 'Азиатские оружия', '1800-01-01', 'Япония, Китай', 1, '80_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (81, 'Азиатские оружия', '1800-01-01', 'Япония, Китай', 1, '81_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (82, 'Азиатская броня', '1800-01-01', 'Япония', 1, '82_5.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (83, 'Винтовки', '1900-01-01', 'Германия', 1, '83_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (84, 'Головные уборы времён Первой и Второй Мировой Войны', '1900-01-01', 'Россия, СССР', 1, '84_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (85, 'Немецкие оружия', '1900-01-01', 'Германия', 1, '85_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (86, 'Немецкие оружия', '1900-01-01', 'Германия', 1, '85_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (87, 'Портрет Сталина И.В.', '1945-01-01', 'СССР', 1, '86.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (88, 'Именные оружия СССР', '1950-01-01', 'СССР', 1, '87_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (89, 'Потрет Н.М. Хлебникова', '1960-01-01', 'Россия', 1, '88.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (90, 'Торговля и промышленность', '1900-01-01', 'Франция', 1, '89_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (91, 'Сборщица колосьев', '1900-01-01', 'Западная Европа', 1, '90.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (92, 'Инсталляция с пушкой', '1900-01-01', 'Россия', 1, '91_1.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (93, 'Инсталляция с пушкой', '1900-01-01', 'Россия', 1, '91_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (94, 'Инсталляция с пушкой', '1900-01-01', 'Россия', 1, '91_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (95, 'Инсталляция с пушкой', '1900-01-01', 'Россия', 1, '91_4.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (96, 'Инсталляция с пушкой', '1900-01-01', 'Россия', 1, '91_5.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (97, 'Инсталляция с пушкой', '1900-01-01', 'Россия', 1, '91_6.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (98, 'Инсталляция с пушкой', '1900-01-01', 'Россия', 1, '91_7.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (99, 'Инсталляция с пушкой', '1900-01-01', 'Россия', 1, '91_8.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (101, 'Инсталляция с пушкой', '1900-01-01', 'Россия', 1, '91_9.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (102, 'Инсталляция с пушкой', '1900-01-01', 'Россия', 1, '91_10.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (103, 'Русская монетная система в серебряных и золотых монетах', '1700-01-01', 'Россия', 7, '92_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (104, 'Серебряная посуда', '1800-01-01', 'Россия', 7, '93_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (105, 'Памятные медали России', '1900-01-01', 'Россия', 7, '94_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (106, 'Грамоты и ларцы', '1900-01-01', 'Россия', 7, '95_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (107, 'Монетная система иностранных государств', '1900-01-01', 'Европа, Азия', 7, '96_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (108, 'Позолоченная и медная посуда', '1900-01-01', 'Россия', 7, '97_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (109, 'Ордена России', '1900-01-01', 'Россия', 7, '98_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (110, 'Религиозные Атрибуты России', '1900-01-01', 'Россия', 7, '99_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (111, 'Ордена иностранных государств', '1900-01-01', 'Европа, Азия', 7, '100_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (112, 'Сабли', '1900-01-01', 'Средняя Азия', 7, '101_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (113, 'Сабли', '1900-01-01', 'Средняя Азия, Кавказ', 7, '102_2.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (114, 'Наградные медали и знаки России', '1800-01-01', 'Россия', 7, '103_3.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (115, 'Универсальные астрономические часы', '1912-01-01', 'Россия', 5, '104_1.jpg');
INSERT INTO public.exhibits_list (exhibit_id, name, date, country, exhibition_number, pic) VALUES (62, 'Вооружение ратника', '1700-01-01', 'Средняя Азия, Персия', 1, '61_Вооружение_ратника.jpg');


--
-- TOC entry 3420 (class 0 OID 50335)
-- Dependencies: 215
-- Data for Name: museums; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.museums (museum_id, museum_name) VALUES (1, 'Бурылинский музей');


--
-- TOC entry 3259 (class 2606 OID 50331)
-- Name: exhibit_observers exhibit_observers_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibit_observers
    ADD CONSTRAINT exhibit_observers_pk PRIMARY KEY (observer_id);


--
-- TOC entry 3253 (class 2606 OID 50319)
-- Name: exhibit_patrons exhibit_patrons_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibit_patrons
    ADD CONSTRAINT exhibit_patrons_pk PRIMARY KEY (patron_id);


--
-- TOC entry 3256 (class 2606 OID 50326)
-- Name: exhibit_review exhibit_review_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibit_review
    ADD CONSTRAINT exhibit_review_pk PRIMARY KEY (review_id);


--
-- TOC entry 3251 (class 2606 OID 50312)
-- Name: exhibit_workers exhibit_workers_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibit_workers
    ADD CONSTRAINT exhibit_workers_pk PRIMARY KEY (worker_id);


--
-- TOC entry 3249 (class 2606 OID 50307)
-- Name: exhibition_list exhibition_list_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibition_list
    ADD CONSTRAINT exhibition_list_pk PRIMARY KEY (exhibition_number);


--
-- TOC entry 3246 (class 2606 OID 50302)
-- Name: exhibits_list exhibits_list_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibits_list
    ADD CONSTRAINT exhibits_list_pk PRIMARY KEY (exhibit_id);


--
-- TOC entry 3261 (class 2606 OID 50339)
-- Name: museums museums_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.museums
    ADD CONSTRAINT museums_pk PRIMARY KEY (museum_id);


--
-- TOC entry 3247 (class 1259 OID 50403)
-- Name: idx_exhibit_exhibition; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_exhibit_exhibition ON public.exhibits_list USING btree (exhibition_number);


--
-- TOC entry 3254 (class 1259 OID 50405)
-- Name: idx_paron_exhibit; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_paron_exhibit ON public.exhibit_patrons USING btree (exhibit_id);


--
-- TOC entry 3257 (class 1259 OID 50404)
-- Name: idx_review_exhibition; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_review_exhibition ON public.exhibit_review USING btree (exhibtion_id);


--
-- TOC entry 3270 (class 2620 OID 50386)
-- Name: exhibits_list trigger_increment_exhibit_id; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trigger_increment_exhibit_id BEFORE INSERT ON public.exhibits_list FOR EACH ROW EXECUTE FUNCTION public.increment_exhibit_id();


--
-- TOC entry 3274 (class 2620 OID 50389)
-- Name: exhibit_observers trigger_increment_observer_id; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trigger_increment_observer_id BEFORE INSERT ON public.exhibit_observers FOR EACH ROW EXECUTE FUNCTION public.increment_observer_id();


--
-- TOC entry 3272 (class 2620 OID 50393)
-- Name: exhibit_review trigger_increment_review_id; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trigger_increment_review_id BEFORE INSERT ON public.exhibit_review FOR EACH ROW EXECUTE FUNCTION public.increment_review_id();


--
-- TOC entry 3271 (class 2620 OID 50391)
-- Name: exhibit_workers trigger_increment_worker_id; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trigger_increment_worker_id BEFORE INSERT ON public.exhibit_workers FOR EACH ROW EXECUTE FUNCTION public.increment_worker_id();


--
-- TOC entry 3273 (class 2620 OID 50383)
-- Name: exhibit_review trigger_set_review_date; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trigger_set_review_date BEFORE INSERT ON public.exhibit_review FOR EACH ROW EXECUTE FUNCTION public.set_review_date();


--
-- TOC entry 3265 (class 2606 OID 50355)
-- Name: exhibit_patrons exhibit_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibit_patrons
    ADD CONSTRAINT exhibit_fk FOREIGN KEY (exhibit_id) REFERENCES public.exhibits_list(exhibit_id);


--
-- TOC entry 3262 (class 2606 OID 50340)
-- Name: exhibits_list exhibitions_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibits_list
    ADD CONSTRAINT exhibitions_fk FOREIGN KEY (exhibition_number) REFERENCES public.exhibition_list(exhibition_number);


--
-- TOC entry 3267 (class 2606 OID 50365)
-- Name: exhibit_review exhibtion_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibit_review
    ADD CONSTRAINT exhibtion_fk FOREIGN KEY (exhibtion_id) REFERENCES public.exhibition_list(exhibition_number);


--
-- TOC entry 3263 (class 2606 OID 50345)
-- Name: exhibition_list museum_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibition_list
    ADD CONSTRAINT museum_fk FOREIGN KEY (museum_id) REFERENCES public.museums(museum_id);


--
-- TOC entry 3264 (class 2606 OID 50350)
-- Name: exhibit_workers museum_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibit_workers
    ADD CONSTRAINT museum_fk FOREIGN KEY (museum_id) REFERENCES public.museums(museum_id);


--
-- TOC entry 3269 (class 2606 OID 50375)
-- Name: exhibit_observers museum_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibit_observers
    ADD CONSTRAINT museum_fk FOREIGN KEY (museum_id) REFERENCES public.museums(museum_id);


--
-- TOC entry 3266 (class 2606 OID 50360)
-- Name: exhibit_patrons observer_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibit_patrons
    ADD CONSTRAINT observer_fk FOREIGN KEY (patron_id) REFERENCES public.exhibit_observers(observer_id);


--
-- TOC entry 3268 (class 2606 OID 50370)
-- Name: exhibit_review observer_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.exhibit_review
    ADD CONSTRAINT observer_fk FOREIGN KEY (observer_id) REFERENCES public.exhibit_observers(observer_id);


-- Completed on 2024-12-18 03:37:42 MSK

--
-- PostgreSQL database dump complete
--

