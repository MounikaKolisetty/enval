import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { HomeComponent } from '../home/home.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserService } from '../services/user.service';
import { CommonModule } from '@angular/common';
import { RecaptchaModule } from 'ng-recaptcha';

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            HomeComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            ScrollButtonComponent,
            ReactiveFormsModule,
            CommonModule,
            RecaptchaModule
  ],
  providers: [UserService],
  templateUrl: './contact.component.html',
  styleUrl: './contact.component.css'
})
export class ContactComponent {
  userForm!: FormGroup;
  messageSent = false;
  captchaResolved: boolean = false; // Variable to track reCAPTCHA status
  captchaResponse: string | null = null;
  constructor(private fb: FormBuilder, private userService: UserService) { }
  ngOnInit(){
    this.userForm = this.fb.group({
      firstName: ['', Validators.required],
      lastName: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      phoneNumber: ['', Validators.required],
      message: ['', Validators.required]
    })
  }
  resolved(captchaResponse: string | null) { 
    console.log('Captcha Response:', captchaResponse); 
    this.captchaResponse = captchaResponse;
    this.captchaResolved = !!captchaResponse; // Set to true if captchaResponse is not empty 
  }
  onSubmit() {
    if(this.userForm.valid && this.captchaResolved){
      const captchaToken = this.captchaResponse ?? ''; 
      this.userService.sendUserContact(this.userForm.value, captchaToken).subscribe(
        response => {
          console.log('Email sent successfully', response); 
          this.messageSent = true;
          this.userForm.reset();
        }, 
        error => { 
          console.error('Error sending email', error); 
        }
      );
    }
  }
}
