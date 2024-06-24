<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h2>Edit Profile</h2>

            <form action="/friendflow/updateProfile" method="POST">

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name"
                        value="<?= htmlspecialchars($data['name']) ?>">
                </div>

                <div class="form-group">
                    <label for="surname">Surname</label>
                    <input type="text" class="form-control" id="surname" name="surname" placeholder="Enter your surname"
                        value="<?= htmlspecialchars($data['surname']) ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"
                        value="<?= htmlspecialchars($data['email']) ?>">
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Enter new password">
                </div>

                <div class="form-group">
                    <label for="password_repeated">Confirm Password</label>
                    <input type="password" class="form-control" id="password_repeated" name="password_repeated"
                        placeholder="Confirm new password">
                </div>

                <?php $birthday = $data['birthday'] ?? '';
                list($year, $month, $day) = explode('-', $birthday);
                ?>

                <label for="birth_year">Year of Birth:</label>
                <select name="birth_year" id="birth_year" required>
                    <?php for ($i = date('Y'); $i >= 1900; $i--): ?>
                        <option value="<?= $i ?>" <?= $i == $year ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <label for="birth_month">Month of Birth:</label>
                <select name="birth_month" id="birth_month" required>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= $i == $month ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <label for="birth_day">Day of Birth:</label>
                <select name="birth_day" id="birth_day" required>
                    <?php for ($i = 1; $i <= 31; $i++): ?>
                        <option value="<?= $i ?>" <?= $i == $day ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>