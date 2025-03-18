import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { UserService } from '../services/user.service';
import { ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { NavbarComponent } from '../navbar/navbar.component';
import { CommonModule } from '@angular/common';
import { FooterComponent } from '../footer/footer.component';


@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
            ReactiveFormsModule,
            HttpClientModule,
            NavbarComponent,
            CommonModule,
            FooterComponent
  ],
  providers: [UserService], // Add UserService as a provider
  templateUrl: './password-reset.component.html',
  styleUrl: './password-reset.component.css'
})
export class PasswordResetComponent implements OnInit{
  passwordResetForm!: FormGroup;
  message: string = '';
  token:string = '';
  passwordFieldType: string = 'password'; 
  showmessage = false;
  showresetform = true;
  verificationStatus: string = 'Verifying...';

  constructor(private fb: FormBuilder, private userService: UserService, private route: ActivatedRoute, private router: Router) {}
    
  ngOnInit() {
     // Capture the token from the query parameters 
     this.route.queryParams.subscribe(params => {
       this.token = params['token']; 
      }); 
      this.passwordResetForm = this.fb.group({
        newPassword: ['', [
            Validators.required,
            Validators.minLength(10),
            Validators.pattern('^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{10,}$')
          ]] })
    }

  togglePasswordVisibility() {
    this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password'; 
  }
  onSubmit() {
    if(this.passwordResetForm.valid)
    {
      const newPassword = this.passwordResetForm.value.newPassword;
      this.userService.resetPassword(this.token, newPassword).subscribe({
        next: (response) => {
          if (response.success) {
            this.showmessage = true;
            this.showresetform = false;
            this.verificationStatus = 'Password updated successfully! Redirecting...';
            setTimeout(() => this.router.navigate(['/login']), 3000);
          } else {
            this.showmessage = true;
            this.showresetform = false;
            this.verificationStatus = 'Verification failed: ' + response.message;
          }
        },
        error: () => {
          this.verificationStatus = 'Error verifying email!';
        }
      });
    }
  }
}
