<div class="container-fluid mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-10 bg-dark-blue-gray custom-text-color custom-border p-4">
            <!-- Upload Profile Image Section -->
            <h2 class="text-center mb-4">Upload Profile Image</h2>
            <form action="/friendflow/upload-profile-image" method="POST" enctype="multipart/form-data">
                <div class="form-group text-center">
                    <label for="profileImageUploadButton" class="btn btn-primary">Choose a profile image</label>
                    <input type="file" class="form-control-file d-none" id="profileImage" name="profileImage" accept="image/*" onchange="previewImage(event)">
                </div>
                <div class="form-group text-center">
                    <img id="imagePreview" src="#" alt="Image Preview" style="display: none; width: 200px; height: 200px; object-fit: contain; margin-top: 10px;">
                </div>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="text-center">
                    <button type="submit" class="btn btn-primary d-none" id="imageUploadBtn">Upload Image</button>
                </div>
            </form>
            <hr class="bg-light">

            <!-- Edit Profile Section -->
            <h2 class="text-center mt-5 mb-4">Edit Profile</h2>
            <form action="/friendflow/updateProfile" method="POST">
                <div class="form-group row justify-content-center">
                    <div class="col-md-8">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" value="<?= htmlspecialchars($data['name']) ?>">
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <div class="col-md-8">
                        <label for="surname">Surname</label>
                        <input type="text" class="form-control" id="surname" name="surname" placeholder="Enter your surname" value="<?= htmlspecialchars($data['surname']) ?>">
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <div class="col-md-8">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($data['email']) ?>">
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <div class="col-md-8">
                        <label for="password">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <div class="col-md-8">
                        <label for="password_repeated">Confirm Password</label>
                        <input type="password" class="form-control" id="password_repeated" name="password_repeated" placeholder="Confirm new password">
                    </div>
                </div>

                <!-- Birthday Section -->
                <?php $birthday = $data['birthday'] ?? '';
                list($year, $month, $day) = explode('-', $birthday);
                ?>
                <div class="form-row justify-content-center">
                    <div class="form-group col-md-3">
                        <label for="birth_year">Year</label>
                        <select class="form-control" name="birth_year" id="birth_year" required>
                            <?php for ($i = date('Y'); $i >= 1900; $i--) : ?>
                                <option value="<?= $i ?>" <?= $i == $year ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="birth_month">Month</label>
                        <select class="form-control" name="birth_month" id="birth_month" required>
                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                <option value="<?= $i ?>" <?= $i == $month ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="birth_day">Day</label>
                        <select class="form-control" name="birth_day" id="birth_day" required>
                            <?php for ($i = 1; $i <= 31; $i++) : ?>
                                <option value="<?= $i ?>" <?= $i == $day ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
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
            imagePreview.classList.add('mx-auto');
        };
        reader.readAsDataURL(input.files[0]);
        document.getElementById('imageUploadBtn').classList.remove('d-none');
    }
</script>