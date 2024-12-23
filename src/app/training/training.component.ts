import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { MatIconModule } from '@angular/material/icon';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserService } from '../services/user.service';
import { RecaptchaModule } from 'ng-recaptcha';

@Component({
  selector: 'app-training',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            CommonModule,
            ScrollButtonComponent,
            MatIconModule,
            RecaptchaModule,
            ReactiveFormsModule
  ],
  providers: [UserService],
  templateUrl: './training.component.html',
  styleUrl: './training.component.css'
})
export class TrainingComponent {
  isContactVisible: boolean = false; 
  userForm!: FormGroup;
  captchaResolved: boolean = false;
  messageSent: boolean = false;
  constructor(private fb: FormBuilder, private userService: UserService) { }
  ngOnInit(){
      this.userForm = this.fb.group({
        name: ['', Validators.required],
        designation: ['', Validators.required],
        organization: ['', Validators.required], 
        location: ['', Validators.required], 
        trainees: ['', Validators.required], 
        email: ['', Validators.required], 
        mobile: ['', Validators.required]
      })
    }
  toggleContact() {
    this.isContactVisible = !this.isContactVisible; 
  } 
  closeContact() {
    this.isContactVisible = false; 
  }
  resolved(captchaResponse: string | null) { 
    console.log('Captcha Response:', captchaResponse); 
    this.captchaResolved = !!captchaResponse; // Set to true if captchaResponse is not empty 
  }
  onSubmit(){
    if(this.userForm.valid && this.captchaResolved){
    // if(this.userForm.valid){
      this.userService.sendTrainingForm(this.userForm.value).subscribe(
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
