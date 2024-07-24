<div class="container-fluid mt-2 mb-4">
    <div class="row">
        <div class="col-sm-3 offset-sm-4">
            <h2>Upload Profile Image</h2>
            <form action="/friendflow/upload-profile-image" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profileImageUploadButton" class="btn btn-primary">Choose a profile image</label>
                    <input type="file" class="form-control-file d-none" id="profileImage" name="profileImage" accept="image/*" onchange="previewImage(event)">
                </div>
                <div class="form-group">
                    <img id="imagePreview" src="#" alt="Image Preview" style="display: none; width: 200px; height: 200px; object-fit: contain; margin-top: 10px;">
                </div>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <button type="submit" class="btn btn-primary d-none" id="imageUploadBtn">Upload Image</button>
            </form>
            <hr class="bg-light">

            <h2 class="mt-5">Edit Profile</h2>
            <form action="/friendflow/updateProfile" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" value="<?= htmlspecialchars($data['name']) ?>">
                </div>

                <div class="form-group">
                    <label for="surname">Surname</label>
                    <input type="text" class="form-control" id="surname" name="surname" placeholder="Enter your surname" value="<?= htmlspecialchars($data['surname']) ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($data['email']) ?>">
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
                </div>

                <div class="form-group">
                    <label for="password_repeated">Confirm Password</label>
                    <input type="password" class="form-control" id="password_repeated" name="password_repeated" placeholder="Confirm new password">
                </div>

                <?php $birthday = $data['birthday'] ?? '';
                list($year, $month, $day) = explode('-', $birthday);
                ?>

                <div class="form-group">
                    <label for="birth_year">Year of Birth:</label>
                    <select class="form-control" name="birth_year" id="birth_year" required>
                        <?php for ($i = date('Y'); $i >= 1900; $i--) : ?>
                            <option value="<?= $i ?>" <?= $i == $year ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="birth_month">Month of Birth:</label>
                    <select class="form-control" name="birth_month" id="birth_month" required>
                        <?php for ($i = 1; $i <= 12; $i++) : ?>
                            <option value="<?= $i ?>" <?= $i == $month ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="birth_day">Day of Birth:</label>
                    <select class="form-control" name="birth_day" id="birth_day" required>
                        <?php for ($i = 1; $i <= 31; $i++) : ?>
                            <option value="<?= $i ?>" <?= $i == $day ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelector('.btn-primary').addEventListener('click', function() {
        document.getElementById('profileImage').click();
    });

    function previewImage(event) {
        var input = event.target;
        var reader = new FileReader();
        reader.onload = function() {
            var dataURL = reader.result;
            var imagePreview = document.getElementById('imagePreview');
            imagePreview.src = dataURL;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
        document.getElementById('imageUploadBtn').classList.remove('d-none');
    }
</script>