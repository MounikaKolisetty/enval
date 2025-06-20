import { Component, OnInit, HostListener } from '@angular/core';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatIconModule } from '@angular/material/icon';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { AuthService } from '../services/auth.service';
import { UserService } from '../services/user.service';
import { tap } from 'rxjs/operators';
import { interval, Subscription } from 'rxjs';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [
    MatToolbarModule, 
    MatIconModule, 
    RouterOutlet, 
    RouterLink, 
    RouterLinkActive, 
    CommonModule,
    HttpClientModule
  ],
  providers: [UserService], // Add UserService as a provider
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.css']
})
export class NavbarComponent implements OnInit {
  userFullName = '';
  userInitials =  '';
  isLoggedIn = false;
  private timerSubscription: Subscription | null = null;
  private logoutTime: number = 300000; // 5 minutes in milliseconds

  constructor(
    private authService: AuthService, 
    private router: Router, 
    private userService: UserService
  ) {}

  ngOnInit() {
    this.authService.currentNameSource.subscribe(userName => {
      this.userFullName = userName;
      this.userInitials = this.getInitials(userName);
    });

    this.authService.currentisLoggedIn.subscribe(loggedIn => {
      this.isLoggedIn = loggedIn;
      if (loggedIn) {
        this.userFullName = localStorage.getItem('UserName') || ''; 
        this.userInitials = this.getInitials(this.userFullName);
        this.startTimer();
      } else {
        this.userFullName = '';
        this.userInitials = '';
        this.stopTimer();
      }
    });

    if (this.LoggedIn()) {
      this.authService.changeIsLoggedin(true);
      this.authService.changeName(localStorage.getItem('UserName') || '');
    }
  }

  getInitials(fullName: string): string {
    const nameParts = fullName.split(' '); 
    if (nameParts.length >= 2) { 
        return nameParts[0].charAt(0).toUpperCase() + nameParts[1].charAt(0).toUpperCase(); 
    } else if (nameParts.length === 1) {
      return nameParts[0].charAt(0).toUpperCase() + nameParts[0].charAt(1).toUpperCase(); 
    } else { 
      return ''; 
    } 
  }

  logout() {
    this.userService.logout().subscribe(
      response => {
        console.log(response.message); 
        localStorage.clear();
        this.authService.changeIsLoggedin(false); // Update login state
        this.authService.changeName(''); // Clear the username
        this.router.navigate(['/login']);
      }, 
      error => { 
        console.error('Error logging out', error); 
      }
    );
  }

  LoggedIn(): boolean {
    return !!localStorage.getItem('UserName'); 
  }

  @HostListener('document:mousemove')
  @HostListener('document:keydown')
  resetTimer() {
    this.stopTimer();
    this.startTimer();
  }

  startTimer() {
    this.timerSubscription = interval(this.logoutTime).subscribe(() => this.logout());
  }

  stopTimer() {
    if (this.timerSubscription) {
      this.timerSubscription.unsubscribe();
    }
  }
}
