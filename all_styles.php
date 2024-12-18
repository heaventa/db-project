<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Музей</title>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<style>
	body {
		font-family: 'Montserrat', sans-serif;
		margin: 0;
		padding: 0;
	}
	header {
		background-color: #DAA06D;
		padding: 20px;
		text-align: center;
	}
	main {
		min-height: 70vh;
		flex: 1;
		padding: 20px;
		display:flex;
		justify-content: center;
		align-items: center;
		text-align: center;
	}
	.login-form,
	.logout-form,
	.role-selection,
	.add,
	.edit,
	.delete,
	.review-form {
		background: #ffffff;
		padding: 20px;
		border-radius: 10px;
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
		width: 300px;
		margin: 10px;
		display:flex;
		flex-direction: column;
	}
	input, 
	textarea,
	select,
	option {
		font-family: 'Montserrat', sans-serif;
		padding: 10px;
		margin: 0 0 10px 0;
		color: black;
		border: 1px solid #ddd;
		border-radius: 10px;
		resize: none;
		box-sizing: border-box;
		transition: border-color 0.3s ease;
		width: 100%;
		background-color: white;
		appearance: none;
		-moz-appearance: none;
		-webkit-appeatance: none;
	}
	input:focus,
	textarea:focus {
		border-color: #800020;
		outline: none;
	}
	.login-form input[type="submit"],
	.login-form button[type="submit"],
	.logout-form input[type="submit"],
	button {
		font-family: 'Montserrat', sans-serif;
		width: 100%;
		padding: 10px;
		margin: 10px 0 0 0;
		border: none;
		border-radius: 10px;
		background-color: #DAA06D;
		color: #ffffff;
		cursor: pointer;
		transition: background-color 0.3s ease;
	}
	.login-form input[type="submit"]:hover,
	.login-form button[type="submit"]:hover,
	.logout-form input[type="submit"]:hover,
	button:hover {
		background-color: #800020;
	}
	.n_button {
		color: white;
		text-decoration: none;
		padding: 10px 20px;
		border-radius: 10px;
		transition: background-color 0.3s;
	}
	.n_button:hover {
		background-color: #800020;
		border-radius: 10px;
	}
	.bigtext {
		color: white;
		font-weight: 500;
		font-size: x-large;
	}
	.midtext_dark {
		color: #999;
		font-weight: 500;
		font-size: x-large;
	}
	.error,
	.success {
		color: #999;
		margin: 0;
	}
	.card,
	.forms,
	.reviews {
		display: flex;
  		align-items: flex-start;
  		flex-wrap: wrap;
  		height: 100%;
  		justify-content: center;
		}
	.table-container,
	.review-table {
		margin: 10px;
		background: #ffffff;
		border-radius: 10px;
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
		overflow: hidden;
		width: 375px;
		border-collapse: collapse;
		height: 20%;
	}
	.big-table {
		margin: 10px;
		background: #ffffff;
		border-radius: 10px;
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
		overflow: hidden;
		width: 90%;
		border-collapse: collapse;
	}
	th {
		background-color: #DAA06D;
		color: #ffffff;
		padding: 10px;
		text-align: center;
	}
	td {
		padding: 10px;
		border-bottom: 1px solid #DAA06D;
		text-align: center;
	}
	td img {
		max-height: 200px; 
		align-items: center;
		border-radius: 10px;
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
	}
	tr:last-child td {
		border-bottom: none;
	}
	footer {
		width: 100%;
		background-color: #DAA06D;
		color: white;
		text-align: center;
		padding: 10px;
	}
</style>
