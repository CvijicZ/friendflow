<div class="container">
    <div class="row justify-content-center">
        <!-- Registration Form -->
        <div class="col-md-6">
            <div class="form-container">
                <h2>Register</h2>
                <form action="/friendflow/register" method="POST">
                    <div class="form-group">
                        <label for="reg-name">Name</label>
                        <input class="form-control" type="text" name="name" placeholder="Name:" required>
                    </div>
                    <div class="form-group">
                        <label for="surname">Surname</label>
                        <input class="form-control" type="text" name="surname" placeholder="Surname:" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input class="form-control" type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input class="form-control" type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label for="password_repeated">Repeat password</label>
                        <input class="form-control" type="password" name="password_repeated" placeholder="Repeat password" required>
                    </div>
                    <div class="form-group">
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

                        <input type="hidden" name="csrf_token"
                            value="<?= \App\Middlewares\CSRFMiddleware::getToken() ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Additional Content Section -->
        <div class="col-md-12">
            <div class="content-section">
                <h3>Why Join Our Network?</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque imperdiet purus eu erat
                    laoreet, quis condimentum purus vestibulum. Aenean at accumsan leo. Sed sed odio eget eros bibendum
                    tincidunt.</p>
                <p>Vivamus volutpat, quam in efficitur pulvinar, justo nunc varius metus, nec placerat massa nunc eget
                    erat. Aliquam erat volutpat. Integer vel mi vel purus accumsan vestibulum.</p>
                <h3>Testimonials</h3>
                <div class="testimonials">
                    <div class="testimonial">
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/50" alt="User">
                            <div>
                                <div class="name">Alice Johnson</div>
                                <div class="role">Web Developer</div>
                            </div>
                        </div>
                        <p>"This social network has completely changed the way I connect with friends and colleagues.
                            The user experience is fantastic!"</p>
                    </div>
                    <div class="testimonial">
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/50" alt="User">
                            <div>
                                <div class="name">Bob Smith</div>
                                <div class="role">Graphic Designer</div>
                            </div>
                        </div>
                        <p>"A great platform for staying in touch and sharing ideas. I highly recommend it to anyone
                            looking to expand their professional network."</p>
                    </div>
                    <div class="testimonial">
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/50" alt="User">
                            <div>
                                <div class="name">Clara Green</div>
                                <div class="role">Content Writer</div>
                            </div>
                        </div>
                        <p>"The community here is amazing. I've met so many like-minded individuals and have grown my
                            personal and professional relationships."</p>
                    </div>
                    <div class="testimonial">
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/50" alt="User">
                            <div>
                                <div class="name">Daniel Brown</div>
                                <div class="role">Digital Marketer</div>
                            </div>
                        </div>
                        <p>"The features and tools provided by this social network are top-notch. It has made networking
                            so much easier and more enjoyable."</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>