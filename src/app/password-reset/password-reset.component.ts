import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
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

  constructor(private fb: FormBuilder, private userService: UserService, private route: ActivatedRoute) {}
    
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
    const newPassword = this.passwordResetForm.value.newPassword;
    this.userService.resetPassword(this.token, newPassword).subscribe(
      response => 
        {
          this.showresetform = false;
          this.showmessage = true;
          console.log('Password has been reset', response );
        }, 
       error =>
        {
          console.log('Error resetting password', error)
        } 
      );
  }
}
