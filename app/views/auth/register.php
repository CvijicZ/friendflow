<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <h1>Create your FREE profile!</h1>
    <form action="/friendflow/register" method="POST">
        <input type="text" name="name" placeholder="Name:" required>
        <input type="text" name="surname" placeholder="Surname:" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <label for="birth_year">Year of Birth:</label>
        <select name="birth_year" id="birth_year" required>
            <?php for ($i = date('Y'); $i >= 1900; $i--): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label for="birth_month">Month of Birth:</label>
        <select name="birth_month" id="birth_month" required>
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label for="birth_day">Day of Birth:</label>
        <select name="birth_day" id="birth_day" required>
            <?php for ($i = 1; $i <= 31; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <button type="submit">Register</button>
    </form>
</body>

</html>